<?php
set_time_limit(0); // Scraper might take time
header('Content-Type: text/plain; charset=utf-8');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'court_app';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database connection failed");
}
$conn->set_charset("utf8mb4");

// Rangamati District ID is likely in the 60s (based on previous tests where Rangpur was 59, and it's alphabetic-ish)
// Let's first find the correct district ID for Rangamati
$baseUrl = 'https://causelist.judiciary.gov.bd';

function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Referer: https://causelist.judiciary.gov.bd/',
        'Accept: application/json, text/html, */*'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

echo "Finding Rangamati District ID...\n";
$divisionsJson = fetchUrl($baseUrl . '/api?path=geo/divisions');
$divisions = json_decode($divisionsJson, true);
$rangamatiDistrictId = null;

foreach ($divisions as $div) {
    $districtsJson = fetchUrl($baseUrl . '/api?path=geo/districts&division_id=' . $div['geo_division_id']);
    $distData = json_decode($districtsJson, true);
    foreach ($distData as $dParent) {
        if (isset($dParent['districts'])) {
            foreach ($dParent['districts'] as $dist) {
                if (strpos($dist['district_name_eng'], 'RANGAMATI') !== false) {
                    $rangamatiDistrictId = $dist['geo_district_id'];
                    echo "Found Rangamati: District ID " . $rangamatiDistrictId . "\n";
                    break 3;
                }
            }
        }
    }
}

if (!$rangamatiDistrictId) {
    die("Rangamati District not found");
}

// Find all courts in Rangamati
$officeOrigins = "4,5,6,7,8,18,19,20,21,74,75,76,83,94,103,105";
$courtsJson = fetchUrl($baseUrl . "/api?path=courts&district_id=$rangamatiDistrictId&office_origin_id=$officeOrigins");
$courts = json_decode($courtsJson, true);

echo "Found " . count($courts) . " courts in Rangamati.\n";

// Table for global causelist cache (to store all cases found)
$conn->query("CREATE TABLE IF NOT EXISTS global_causelist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    court_id INT,
    court_name VARCHAR(255),
    case_no VARCHAR(100),
    activity TEXT,
    next_date VARCHAR(50),
    order_text TEXT,
    cause_date VARCHAR(50),
    scraped_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (court_id, case_no, cause_date)
)");

// Check if cause_date column exists, if not add it
$res = $conn->query("SHOW COLUMNS FROM global_causelist LIKE 'cause_date'");
if ($res->num_rows == 0) {
    $conn->query("ALTER TABLE global_causelist ADD COLUMN cause_date VARCHAR(50) AFTER order_text");
    $conn->query("ALTER TABLE global_causelist DROP INDEX court_id");
    $conn->query("ALTER TABLE global_causelist ADD UNIQUE KEY (court_id, case_no, cause_date)");
}

// Scrape for next 7 days
for ($i = 0; $i < 7; $i++) {
    $date = date('d-m-Y', strtotime("+$i days"));
    echo "Scraping for date: $date\n";
    
    foreach ($courts as $court) {
        $courtId = $court['id'];
        $courtName = $court['office_name_bng'];
        
        $url = $baseUrl . "/causelist?courtId=$courtId&date=$date";
        $html = fetchUrl($url);
        
        // Simple HTML parsing for cases
        if (preg_match('/<tbody>(.*?)<\/tbody>/s', $html, $tbody)) {
            if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $tbody[1], $rows)) {
                foreach ($rows[1] as $row) {
                    if (preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $row, $cells)) {
                        $cellData = [];
                        foreach ($cells[1] as $cell) {
                            $cellData[] = trim(strip_tags($cell));
                        }
                        
                        if (count($cellData) >= 3) {
                            $caseNo = $cellData[1];
                            $activity = $cellData[2];
                            $nextDate = isset($cellData[3]) ? $cellData[3] : '';
                            $order = isset($cellData[4]) ? $cellData[4] : '';
                            
                            $stmt = $conn->prepare("INSERT INTO global_causelist (court_id, court_name, case_no, activity, next_date, order_text) 
                                                   VALUES (?, ?, ?, ?, ?, ?) 
                                                   ON DUPLICATE KEY UPDATE 
                                                   activity = VALUES(activity), 
                                                   next_date = VALUES(next_date), 
                                                   order_text = VALUES(order_text)");
                            $stmt->bind_param("isssss", $courtId, $courtName, $caseNo, $activity, $nextDate, $order);
                            $stmt->execute();
                        }
                    }
                }
            }
        }
        echo "."; // Progress
    }
    echo "\n";
}

echo "Scraping completed for Rangamati courts.\n";
?>
