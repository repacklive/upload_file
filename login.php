<?php
session_start();
include 'config.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Mã hóa mật khẩu bằng MD5

    // Kiểm tra thông tin đăng nhập trong cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Thông tin đăng nhập không chính xác.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet"> <!-- Tham chiếu đến tệp CSS -->
</head>
<body>
<div class="login-container">
    <h2>Đăng Nhập</h2>
    <form action="login.php" method="POST">
        <label for="username" class="form-label">Tên đăng nhập</label>
        <input type="text" name="username" id="username" class="form-control" required>

        <label for="password" class="form-label">Mật khẩu</label>
        <input type="password" name="password" id="password" class="form-control" required>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Đăng Nhập</button>
    </form>
</div>
</body>
</html>
