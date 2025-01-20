<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../classes/Database.php';
require_once '../classes/User.php';

// ตรวจสอบว่า Method เป็น POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

// ดึงข้อมูล JSON จาก Request Body
$input = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่ามีข้อมูลที่จำเป็นครบหรือไม่
if (!isset($input['username'], $input['email'], $input['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing required fields: username, email, or password']);
    exit;
}

$username = $input['username'];
$email = $input['email'];
$password = $input['password'];

try {
    $database = new Database();
    $db = $database->connect();

    $user = new User($db);
    $result = $user->register($username, $email, $password);

    if ($result['success']) {
        http_response_code(201); // Created
        echo json_encode(['message' => 'Registration successful']);
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => $result['message']]);
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Something went wrong: ' . $e->getMessage()]);
}
?>
