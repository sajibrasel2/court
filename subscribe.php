<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$db_host = 'localhost';
$db_user = 'techandc_bot';
$db_pass = '12345Sajibs6@';
$db_name = 'techandc_court';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}
$conn->set_charset("utf8mb4");

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'subscribe':
        $user_id = intval($_POST['user_id'] ?? 0);
        $endpoint = $_POST['endpoint'] ?? '';
        $p256dh = $_POST['p256dh'] ?? '';
        $auth_key = $_POST['auth_key'] ?? '';
        if (!$user_id || !$endpoint) {
            echo json_encode(['error' => 'Missing data']);
            break;
        }
        // Upsert subscription
        $stmt = $conn->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth_key) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE endpoint = VALUES(endpoint), p256dh = VALUES(p256dh), auth_key = VALUES(auth_key)");
        $stmt->bind_param("isss", $user_id, $endpoint, $p256dh, $auth_key);
        $stmt->execute();
        echo json_encode(['success' => true]);
        break;

    case 'get_notifications':
        $user_id = intval($_GET['user_id'] ?? 0);
        if (!$user_id) {
            echo json_encode(['error' => 'user_id required']);
            break;
        }
        $res = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id AND is_read = 0 ORDER BY created_at DESC LIMIT 20");
        $notifs = [];
        while ($row = $res->fetch_assoc()) {
            $notifs[] = [
                'id' => $row['id'],
                'case_no' => $row['case_no'],
                'court_name' => $row['court_name'],
                'next_date' => $row['next_date'],
                'message' => $row['message'],
                'created_at' => $row['created_at']
            ];
        }
        echo json_encode($notifs, JSON_UNESCAPED_UNICODE);
        break;

    case 'mark_read':
        $user_id = intval($_POST['user_id'] ?? 0);
        $notif_id = intval($_POST['notif_id'] ?? 0);
        if (!$user_id) break;
        if ($notif_id) {
            $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $notif_id AND user_id = $user_id");
        } else {
            $conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
        }
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}

$conn->close();
?>
