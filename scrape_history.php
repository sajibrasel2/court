<?php
set_time_limit(0);
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

// Ensure global_causelist table exists
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

echo "Starting historical data collection for the past 1 year...\n";

// Get all divisions
$divisionsJson = fetchUrl($baseUrl . '/api?path=geo/divisions');
$divisions = json_decode($divisionsJson, true);

if (!is_array($divisions)) {
    die("Failed to fetch divisions.");
}

$officeOrigins = "4,5,6,7,8,18,19,20,21,74,75,76,83,94,103,105";

foreach ($divisions as $div) {
    echo "Processing Division: " . $div['division_name_eng'] . "\n";
    $districtsJson = fetchUrl($baseUrl . '/api?path=geo/districts&division_id=' . $div['geo_division_id']);
    $distData = json_decode($districtsJson, true);
    
    if (!is_array($distData)) continue;

    foreach ($distData as $dParent) {
        if (!isset($dParent['districts'])) continue;
        foreach ($dParent['districts'] as $dist) {
            $distId = $dist['geo_district_id'];
            echo "  District: " . $dist['district_name_eng'] . " (ID: $distId)\n";
            
            $courtsJson = fetchUrl($baseUrl . "/api?path=courts&district_id=$distId&office_origin_id=$officeOrigins");
            $courts = json_decode($courtsJson, true);
            
            if (!is_array($courts)) continue;

            echo "    Found " . count($courts) . " courts.\n";

            // Loop through past 365 days
            for ($i = 0; $i < 365; $i++) {
                $date = date('d-m-Y', strtotime("-$i days"));
                
                foreach ($courts as $court) {
                    $courtId = $court['id'];
                    $courtName = $court['office_name_bng'];
                    
                    $url = $baseUrl . "/causelist?courtId=$courtId&date=$date";
                    $html = fetchUrl($url);
                    
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
                                        
                                        $stmt = $conn->prepare("INSERT INTO global_causelist (court_id, court_name, case_no, activity, next_date, order_text, cause_date) 
                                                               VALUES (?, ?, ?, ?, ?, ?, ?) 
                                                               ON DUPLICATE KEY UPDATE 
                                                               activity = VALUES(activity), 
                                                               next_date = VALUES(next_date), 
                                                               order_text = VALUES(order_text)");
                                        $stmt->bind_param("issssss", $courtId, $courtName, $caseNo, $activity, $nextDate, $order, $date);
                                        $stmt->execute();
                                    }
                                }
                            }
                        }
                    }
                }
                // Optional: add a small delay to avoid overwhelming the server
                // usleep(100000); 
            }
        }
    }
}

echo "Historical data collection completed.\n";
?>
