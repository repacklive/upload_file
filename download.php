<?php
session_start();
include 'config.php';
require_once '/geetest/lib/class.geetestlib.php';

$GtSdk = new GeetestLib($CAPTCHA_ID, $PRIVATE_KEY);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = array(
        "user_id" => $_SESSION['user_id'],
        "client_type" => "web",
        "ip_address" => $_SERVER["REMOTE_ADDR"]
    );

    $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);

    if ($result) {
        if (isset($_POST['file'])) {
            $fileName = basename($_POST['file']);

            $stmt = $conn->prepare("SELECT file_path, download_count FROM uploaded_files WHERE file_name = ?");
            $stmt->bind_param("s", $fileName);
            $stmt->execute();
            $stmt->bind_result($filePath, $downloadCount);
            $stmt->fetch();
            $stmt->close();

            if ($filePath && file_exists($filePath)) {
                $downloadCount++;
                $stmt = $conn->prepare("UPDATE uploaded_files SET download_count = ? WHERE file_name = ?");
                $stmt->bind_param("is", $downloadCount, $fileName);
                $stmt->execute();
                $stmt->close();

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($filePath));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            } else {
                echo 'File không tồn tại.';
            }
        }
    } else {
        echo 'Xác thực CAPTCHA thất bại, vui lòng thử lại.';
    }
} else {
    echo 'Yêu cầu không hợp lệ.';
}
?>
