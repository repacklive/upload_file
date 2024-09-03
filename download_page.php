<?php
include 'config.php';
require_once '/geetest/lib/class.geetestlib.php';

if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']);

    $stmt = $conn->prepare("SELECT file_path, uploaded_at, download_count FROM uploaded_files WHERE file_name = ?");
    $stmt->bind_param("s", $fileName);
    $stmt->execute();
    $stmt->bind_result($filePath, $uploadedAt, $downloadCount);
    $stmt->fetch();
    $stmt->close();

    if ($filePath) {
        $expiryDate = date('Y-m-d', strtotime($uploadedAt . ' + 7 days'));

        $GtSdk = new GeetestLib($CAPTCHA_ID, $PRIVATE_KEY);
        $data = array(
            "user_id" => "test",
            "client_type" => "web",
            "ip_address" => $_SERVER["REMOTE_ADDR"]
        );
        $status = $GtSdk->pre_process($data, 1);
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $data['user_id'];
        $geetestResponse = $GtSdk->get_response_str();

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Download File</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="css/styles.css" rel="stylesheet">
        </head>
        <body>
        <div class="download-page">
            <div class="file-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h2><?php echo htmlspecialchars($fileName); ?></h2>
            <p>Kích thước file: <?php echo round(filesize($filePath) / 1048576, 2); ?> MB</p>

            <div class="banner-ad">
                <img src="path/to/your/banner1.jpg" alt="Ad Banner">
            </div>

            <div id="timer">Đang chuẩn bị tải xuống...</div>

            <div id="captcha-container"></div>

            <form action="download.php" method="POST" id="downloadForm">
                <input type="hidden" name="file" value="<?php echo urlencode($fileName); ?>">
                <input type="hidden" name="geetest_challenge" id="geetest_challenge">
                <input type="hidden" name="geetest_validate" id="geetest_validate">
                <input type="hidden" name="geetest_seccode" id="geetest_seccode">
                <button type="submit" class="download-btn" id="downloadBtn" disabled>Tải xuống</button>
            </form>

            <div class="banner-ad">
                <img src="path/to/your/banner2.jpg" alt="Ad Banner">
            </div>

            <div class="additional-info">
                <p>Số lượt tải xuống: <?php echo $downloadCount; ?></p>
                <p>File sẽ hết hạn vào ngày <?php echo $expiryDate; ?>.</p>
            </div>
        </div>
        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
        <script src="https://static.geetest.com/static/tools/gt.js"></script>
        <script>
            var timer = 5;
            var interval = setInterval(function() {
                document.getElementById('timer').textContent = 'Vui lòng chờ ' + timer + ' giây...';
                timer--;
                if (timer < 0) {
                    clearInterval(interval);
                    document.getElementById('timer').textContent = 'Đã sẵn sàng tải xuống!';
                    document.getElementById('downloadBtn').disabled = false;
                }
            }, 1000);

            var handlerEmbed = function (captchaObj) {
                captchaObj.onSuccess(function () {
                    var result = captchaObj.getValidate();
                    document.getElementById("geetest_challenge").value = result.geetest_challenge;
                    document.getElementById("geetest_validate").value = result.geetest_validate;
                    document.getElementById("geetest_seccode").value = result.geetest_seccode;
                    document.getElementById("downloadBtn").disabled = false;
                });

                captchaObj.appendTo("#captcha-container");
            };

            $.ajax({
                url: "geetest_init.php",
                type: "get",
                dataType: "json",
                success: function (data) {
                    initGeetest({
                        gt: data.gt,
                        challenge: data.challenge,
                        offline: !data.success,
                        new_captcha: data.new_captcha,
                        product: "embed",
                        width: "100%"
                    }, handlerEmbed);
                }
            });
        </script>
        </body>
        </html>
        <?php
    } else {
        echo "File không tồn tại.";
    }
} else {
    echo "Không có file nào được yêu cầu.";
}
?>
