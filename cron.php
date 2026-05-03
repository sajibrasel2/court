<?php
// Cron job: Check upcoming case dates and create notifications + WhatsApp
// Run daily via cPanel cron: 0 8 * * * php /home/techandc/public_html/court/cron.php

$db_host = 'localhost';
$db_user = 'techandc_bot';
$db_pass = '12345Sajibs6@';
$db_name = 'techandc_court';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("DB Connection failed\n");
}
$conn->set_charset("utf8mb4");

// ===== WhatsApp Config (UltraMsg) =====
// Sign up at https://ultramsg.com - free tier available
// Scan QR, get Instance ID and Token
$WHATSAPP_INSTANCE = 'instance172977';
$WHATSAPP_TOKEN = '4t8k67fi2hw233sp';
$WHATSAPP_ENABLED = !empty($WHATSAPP_INSTANCE) && !empty($WHATSAPP_TOKEN);

// Get today and tomorrow in both formats
$todayEng = date('d-m-Y');
$tomorrowEng = date('d-m-Y', strtotime('+1 day'));
$todayBn = convertToBn($todayEng);
$tomorrowBn = convertToBn($tomorrowEng);

// Get all cases with upcoming dates (include whatsapp_number)
$sql = "SELECT c.user_id, c.case_no, c.court_name, c.next_date, u.phone, u.name as user_name, u.whatsapp_number
        FROM cases c 
        JOIN users u ON c.user_id = u.id
        WHERE c.next_date IS NOT NULL 
        AND c.next_date != '' 
        AND c.next_date != 'N/A'
        ORDER BY c.user_id";

$result = $conn->query($sql);
$notificationsCreated = 0;
$todayNotifs = 0;
$tomorrowNotifs = 0;
$whatsappSent = 0;

while ($row = $result->fetch_assoc()) {
    $nextDate = trim($row['next_date']);
    $userId = $row['user_id'];
    $caseNo = $row['case_no'];
    $courtName = $row['court_name'];
    $whatsappNum = $row['whatsapp_number'];
    
    // Check if date matches today or tomorrow (support both Eng and Bn formats)
    $isToday = ($nextDate === $todayEng || $nextDate === $todayBn || strpos($nextDate, $todayEng) !== false);
    $isTomorrow = ($nextDate === $tomorrowEng || $nextDate === $tomorrowBn || strpos($nextDate, $tomorrowEng) !== false);
    
    if (!$isToday && !$isTomorrow) continue;
    
    // Check if notification already sent today for this case+user
    $stmt = $conn->prepare("SELECT id FROM notifications WHERE user_id = ? AND case_no = ? AND DATE(created_at) = CURDATE()");
    $stmt->bind_param("is", $userId, $caseNo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) continue; // Already notified today
    
    // Create notification message
    if ($isToday) {
        $message = "আজ ({$todayBn}) আপনার মামলা নং {$caseNo} এর তারিখ - {$courtName}";
        $todayNotifs++;
    } else {
        $message = "আগামীকাল ({$tomorrowBn}) আপনার মামলা নং {$caseNo} এর তারিখ - {$courtName}";
        $tomorrowNotifs++;
    }
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, case_no, court_name, next_date, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $caseNo, $courtName, $nextDate, $message);
    $stmt->execute();
    $notificationsCreated++;
    
    // Send WhatsApp message if number exists and API is configured
    if ($WHATSAPP_ENABLED && !empty($whatsappNum)) {
        $waMessage = "⚖️ আমার মামলা আপডেট\n\n{$message}\n\n- আমার মামলা অ্যাপ";
        $waResult = sendWhatsApp($whatsappNum, $waMessage);
        if ($waResult) $whatsappSent++;
    }
}

// Clean up old notifications (older than 7 days, already read)
$conn->query("DELETE FROM notifications WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)");

// Clean up old unread notifications (older than 30 days)
$conn->query("DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");

echo "Cron completed: {$notificationsCreated} notifications created (Today: {$todayNotifs}, Tomorrow: {$tomorrowNotifs}), WhatsApp sent: {$whatsappSent}\n";

$conn->close();

function sendWhatsApp($phone, $message) {
    global $WHATSAPP_INSTANCE, $WHATSAPP_TOKEN;
    
    // Format phone number: add country code if needed
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        $phone = '880' . substr($phone, 1); // Bangladesh
    }
    
    $url = "https://api.ultramsg.com/{$WHATSAPP_INSTANCE}/messages/chat";
    $data = http_build_query([
        'token' => $WHATSAPP_TOKEN,
        'to' => $phone,
        'body' => $message
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

function convertToBn($engDate) {
    $bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $parts = explode('-', $engDate);
    $bnDate = '';
    foreach ($parts as $i => $part) {
        if ($i > 0) $bnDate .= '-';
        for ($j = 0; $j < strlen($part); $j++) {
            $bnDate .= $bnDigits[intval($part[$j])];
        }
    }
    return $bnDate;
}
?>
