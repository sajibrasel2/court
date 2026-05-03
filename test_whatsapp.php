<?php
// Test WhatsApp message to all users with whatsapp_number
// Delete after testing

$db_host = 'localhost';
$db_user = 'techandc_bot';
$db_pass = '12345Sajibs6@';
$db_name = 'techandc_court';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("DB Connection failed\n");
}
$conn->set_charset("utf8mb4");

$WHATSAPP_INSTANCE = 'instance172977';
$WHATSAPP_TOKEN = '4t8k67fi2hw233sp';

// Get all users with whatsapp_number
$result = $conn->query("SELECT id, name, phone, whatsapp_number FROM users WHERE whatsapp_number IS NOT NULL AND whatsapp_number != ''");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

if (empty($users)) {
    // No users with whatsapp_number yet, use phone number as fallback
    echo "No users with whatsapp_number found. Using phone numbers instead...\n\n";
    $result = $conn->query("SELECT id, name, phone, whatsapp_number FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

if (empty($users)) {
    die("No users found in database.\n");
}

echo "Found " . count($users) . " users. Sending test messages...\n\n";

foreach ($users as $u) {
    $phone = $u['whatsapp_number'] ?: $u['phone'];
    $name = $u['name'];
    
    // Format phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        $phone = '880' . substr($phone, 1);
    }
    
    $message = "⚖️ আমার মামলা অ্যাপ\n\nসুপ্রিয় {$name}, এটি একটি টেস্ট মেসেজ। WhatsApp নোটিফিকেশন সফলভাবে কাজ করছে!\n\n- আমার মামলা অ্যাপ";
    
    $url = "https://api.ultramsg.com/{$WHATSAPP_INSTANCE}/messages/chat";
    $data = http_build_query([
        'token' => $WHATSAPP_TOKEN,
        'to' => '+' . $phone,
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
    
    $sent = $httpCode === 200 ? "✅ SENT" : "❌ FAILED (HTTP $httpCode)";
    echo "User: {$name} | Phone: +{$phone} | {$sent}\n";
    if ($httpCode !== 200) echo "Response: {$response}\n";
    
    // Small delay to avoid rate limiting
    sleep(1);
}

echo "\nDone! Delete this file now: rm ~/public_html/court/test_whatsapp.php\n";
$conn->close();
?>
