<?php
$host = 'localhost';
$db = 'file_sharing';
$user = 'root';
$pass = 'your_password';
$CAPTCHA_ID = 'your_captcha_id';
$PRIVATE_KEY = 'your_private_key';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>


