<?php
session_start();
include 'config.php';
require_once '/geetest/lib/class.geetestlib.php';

$GtSdk = new GeetestLib($CAPTCHA_ID, $PRIVATE_KEY);

$data = array(
    "user_id" => "test",
    "client_type" => "web",
    "ip_address" => $_SERVER["REMOTE_ADDR"]
);

$status = $GtSdk->pre_process($data, 1);
$_SESSION['gtserver'] = $status;
$_SESSION['user_id'] = $data['user_id'];
echo $GtSdk->get_response_str();
?>
