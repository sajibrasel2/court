<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$baseUrl = 'https://causelist.judiciary.gov.bd';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// DB Connection
$db_host = 'localhost';
$db_user = 'techandc_bot';
$db_pass = '12345Sajibs6@';
$db_name = 'techandc_court';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}
$conn->set_charset("utf8mb4");

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
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['data' => $response, 'code' => $httpCode];
}

switch ($action) {
    case 'divisions':
        $result = fetchUrl($baseUrl . '/api?path=geo/divisions');
        $data = json_decode($result['data'], true);
        $filtered = [];
        if (is_array($data)) {
            foreach ($data as $item) {
                $filtered[] = [
                    'geo_division_id' => $item['geo_division_id'],
                    'division_name_bng' => $item['division_name_bng'],
                    'division_name_eng' => $item['division_name_eng']
                ];
            }
        }
        echo json_encode($filtered, JSON_UNESCAPED_UNICODE);
        break;

    case 'districts':
        $divisionId = isset($_GET['division_id']) ? intval($_GET['division_id']) : '';
        $url = $baseUrl . '/api?path=geo/districts';
        if ($divisionId) $url .= '&division_id=' . $divisionId;
        $result = fetchUrl($url);
        $data = json_decode($result['data'], true);
        $filtered = [];
        if (is_array($data)) {
            foreach ($data as $div) {
                $districts = [];
                if (isset($div['districts']) && is_array($div['districts'])) {
                    foreach ($div['districts'] as $d) {
                        $districts[] = [
                            'geo_district_id' => $d['geo_district_id'],
                            'district_name_bng' => $d['district_name_bng'],
                            'district_name_eng' => $d['district_name_eng']
                        ];
                    }
                }
                $filtered[] = ['districts' => $districts];
            }
        }
        echo json_encode($filtered, JSON_UNESCAPED_UNICODE);
        break;

    case 'courts':
        $districtId = isset($_GET['district_id']) ? intval($_GET['district_id']) : '';
        $officeOriginId = isset($_GET['office_origin_id']) ? $_GET['office_origin_id'] : '';
        $url = $baseUrl . '/api?path=courts';
        if ($districtId) $url .= '&district_id=' . $districtId;
        if ($officeOriginId) $url .= '&office_origin_id=' . $officeOriginId;
        $result = fetchUrl($url);
        echo $result['data'];
        break;

    case 'all_courts':
        $result = fetchUrl($baseUrl . '/api?path=office/allOfficeUnitName');
        echo $result['data'];
        break;

    case 'causelist':
        $courtId = isset($_GET['courtId']) ? intval($_GET['courtId']) : 0;
        $date = isset($_GET['date']) ? $_GET['date'] : '';
        if (!$courtId) {
            echo json_encode(['error' => 'courtId is required']);
            break;
        }
        // Convert YYYY-MM-DD to DD-MM-YYYY for upstream API
        if ($date && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            $date = $m[3] . '-' . $m[2] . '-' . $m[1];
        }
        $url = $baseUrl . '/causelist?courtId=' . $courtId;
        if ($date) $url .= '&date=' . urlencode($date);
        $result = fetchUrl($url);
        // Parse HTML to extract case data
        $html = $result['data'];
        $cases = parseCauselistHtml($html);
        
        // --- Real-time DB Update Logic ---
        if (!empty($cases['cases']) && $courtId) {
            $stmt = $conn->prepare("INSERT INTO global_causelist (court_id, court_name, case_no, activity, next_date, order_text, cause_date) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE 
                                   activity = VALUES(activity), 
                                   next_date = VALUES(next_date), 
                                   order_text = VALUES(order_text)");
            $courtName = $cases['court_name'];
            foreach ($cases['cases'] as $c) {
                $stmt->bind_param("issssss", $courtId, $courtName, $c['case_no'], $c['activity'], $c['next_date'], $c['order'], $date);
                $stmt->execute();
            }
        }
        
        echo json_encode($cases, JSON_UNESCAPED_UNICODE);
        break;

    case 'caseinfo':
        $courtId = isset($_GET['courtId']) ? intval($_GET['courtId']) : 0;
        $caseno = isset($_GET['caseno']) ? $_GET['caseno'] : '';
        if (!$courtId || !$caseno) {
            echo json_encode(['error' => 'courtId and caseno are required']);
            break;
        }
        $url = $baseUrl . '/caseinfo?courtId=' . $courtId . '&caseno=' . urlencode($caseno);
        $result = fetchUrl($url);
        $html = $result['data'];
        $cases = parseCauselistHtml($html);

        // --- Real-time DB Update Logic for single case ---
        if (!empty($cases['cases']) && $courtId) {
            $stmt = $conn->prepare("INSERT INTO global_causelist (court_id, court_name, case_no, activity, next_date, order_text, cause_date) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE 
                                   activity = VALUES(activity), 
                                   next_date = VALUES(next_date), 
                                   order_text = VALUES(order_text)");
            $courtName = $cases['court_name'];
            $today = date('d-m-Y');
            foreach ($cases['cases'] as $c) {
                $stmt->bind_param("issssss", $courtId, $courtName, $c['case_no'], $c['activity'], $c['next_date'], $c['order'], $today);
                $stmt->execute();
            }
        }

        echo json_encode($cases, JSON_UNESCAPED_UNICODE);
        break;

    // --- Database Actions ---
    case 'auth':
        $phone = $_POST['phone'] ?? '';
        $pin = $_POST['pin'] ?? '';
        $security_answer = $_POST['security_answer'] ?? '';
        $mode = $_POST['mode'] ?? 'login'; // login, register, recovery

        if ($mode === 'register') {
            if (!$phone || !$pin || !$security_answer) {
                echo json_encode(['error' => 'সবগুলো ঘর পূরণ করুন']);
                break;
            }
            $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                echo json_encode(['error' => 'এই নম্বরটি ইতিমধ্যে রেজিস্টার্ড']);
                break;
            }
            $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
            $hashedAnswer = password_hash(strtolower($security_answer), PASSWORD_DEFAULT);
            $whatsapp = $_POST['whatsapp_number'] ?? '';
            $stmt = $conn->prepare("INSERT INTO users (phone, whatsapp_number, pin, security_answer, name) VALUES (?, ?, ?, ?, ?)");
            $name = 'User ' . substr($phone, -4);
            $stmt->bind_param("sssss", $phone, $whatsapp, $hashedPin, $hashedAnswer, $name);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'user_id' => $conn->insert_id, 'name' => $name, 'new' => true]);
            } else {
                echo json_encode(['error' => 'রেজিস্ট্রেশন ব্যর্থ']);
            }
        } elseif ($mode === 'recovery') {
            if (!$phone || !$pin || !$security_answer) {
                echo json_encode(['error' => 'সবগুলো ঘর পূরণ করুন']);
                break;
            }
            $stmt = $conn->prepare("SELECT id, security_answer FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            if ($user && password_verify(strtolower($security_answer), $user['security_answer'])) {
                $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET pin = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPin, $user['id']);
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => 'পিন সফলভাবে পরিবর্তন হয়েছে']);
            } else {
                echo json_encode(['error' => 'তথ্য সঠিক নয়']);
            }
        } else {
            // Login
            $stmt = $conn->prepare("SELECT id, name, pin FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            if ($user && password_verify($pin, $user['pin'])) {
                echo json_encode(['success' => true, 'user_id' => $user['id'], 'name' => $user['name']]);
            } else {
                echo json_encode(['error' => 'নম্বর বা পিন সঠিক নয়']);
            }
        }
        break;

    case 'sync_up':
        $user_id = intval($_POST['user_id'] ?? 0);
        $cases = json_decode($_POST['cases'] ?? '[]', true);
        if (!$user_id) break;

        // Delete existing cases for this user to avoid complex merging for now
        $conn->query("DELETE FROM cases WHERE user_id = $user_id");
        
        $stmt = $conn->prepare("INSERT INTO cases (user_id, court_id, court_name, case_no, plaintiff, defendant, last_activity, prev_date, next_date, last_order, pdf_name, pdf_data, history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($cases as $c) {
            $historyJson = json_encode($c['history'] ?? []);
            $stmt->bind_param("iissssssssssb", 
                $user_id, 
                $c['courtId'], 
                $c['courtName'], 
                $c['case_no'], 
                $c['plaintiff'], 
                $c['defendant'], 
                $c['last_activity'], 
                $c['prev_date'], 
                $c['next_date'], 
                $c['last_order'], 
                $c['pdfName'],
                $null, // pdf_data placeholder
                $historyJson
            );
            
            if (!empty($c['pdfData'])) {
                $stmt->send_long_data(11, base64_decode(preg_replace('#^data:application/pdf;base64,#', '', $c['pdfData'])));
            }
            $stmt->execute();
        }
        echo json_encode(['success' => true]);
        break;

    case 'sync_down':
        $user_id = intval($_GET['user_id'] ?? 0);
        if (!$user_id) break;

        $res = $conn->query("SELECT * FROM cases WHERE user_id = $user_id");
        $cases = [];
        while ($row = $res->fetch_assoc()) {
            $pdfBase64 = '';
            if ($row['pdf_data']) {
                $pdfBase64 = 'data:application/pdf;base64,' . base64_encode($row['pdf_data']);
            }
            $cases[] = [
                'courtId' => intval($row['court_id']),
                'courtName' => $row['court_name'],
                'case_no' => $row['case_no'],
                'plaintiff' => $row['plaintiff'],
                'defendant' => $row['defendant'],
                'last_activity' => $row['last_activity'],
                'prev_date' => $row['prev_date'],
                'next_date' => $row['next_date'],
                'last_order' => $row['last_order'],
                'pdfName' => $row['pdf_name'],
                'pdfData' => $pdfBase64,
                'history' => json_decode($row['history'], true),
                'last_checked' => $row['last_checked']
            ];
        }
        echo json_encode($cases, JSON_UNESCAPED_UNICODE);
        break;

    case 'update_profile':
        $user_id = intval($_POST['user_id'] ?? 0);
        $name = $_POST['name'] ?? '';
        if (!$user_id) break;
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $user_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action.']);
}

function parseCauselistHtml($html) {
    $result = [
        'court_name' => '',
        'date' => '',
        'cases' => []
    ];

    // Extract court name
    if (preg_match('/class="office-unit-name[^"]*">(.*?)<\/div>/s', $html, $m)) {
        $result['court_name'] = trim(strip_tags($m[1]));
    }

    // Extract date
    if (preg_match('/class="cause-date[^"]*">(.*?)<\/div>/s', $html, $m)) {
        $result['date'] = trim(strip_tags($m[1]));
    }

    // Extract table rows - cases
    if (preg_match('/<tbody>(.*?)<\/tbody>/s', $html, $tbody)) {
        if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $tbody[1], $rows)) {
            foreach ($rows[1] as $row) {
                if (preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $row, $cells)) {
                    $cellData = [];
                    foreach ($cells[1] as $cell) {
                        $cellData[] = trim(strip_tags($cell));
                    }
                    if (count($cellData) >= 3) {
                        $result['cases'][] = [
                            'sl' => $cellData[0],
                            'case_no' => isset($cellData[1]) ? $cellData[1] : '',
                            'activity' => isset($cellData[2]) ? $cellData[2] : '',
                            'next_date' => isset($cellData[3]) ? $cellData[3] : '',
                            'order' => isset($cellData[4]) ? $cellData[4] : ''
                        ];
                    }
                }
            }
        }
    }

    return $result;
}
