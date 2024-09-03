<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Sharing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet"> <!-- Tham chiếu đến tệp CSS -->
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Chia sẻ file</h1>

    <!-- Chỉ hiển thị form tải lên nếu người dùng đã đăng nhập -->
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="fileToUpload" class="form-label">Chọn file để tải lên:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Upload File</button>
        </form>
        <div class="text-center mt-4">
            <a href="logout.php" class="btn btn-danger">Đăng Xuất</a>
        </div>
    <?php else: ?>
        <div class="text-center mt-4">
            <a href="login.php" class="btn btn-primary">Đăng Nhập để Tải lên</a>
        </div>
    <?php endif; ?>

    <h2 class="mt-5">Các file đã tải lên</h2>
    <ul class="list-group">
        <?php
        // Lấy danh sách file đã tải lên
        $result = $conn->query("SELECT file_name, file_path FROM uploaded_files ORDER BY uploaded_at DESC");

        // Hiển thị danh sách file
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<li class="list-group-item"><a href="download.php?file=' . urlencode($row['file_name']) . '">' . htmlspecialchars($row['file_name']) . '</a></li>';
            }
        } else {
            echo '<li class="list-group-item">Chưa có file nào được tải lên.</li>';
        }

        // Đóng kết nối
        $conn->close();
        ?>
    </ul>
</div>
</body>
</html>
