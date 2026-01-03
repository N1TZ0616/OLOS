<?php
session_start();
require "config/db.php";

$fid = intval($_POST['feedback_id']);
$msg = mysqli_real_escape_string($conn,$_POST['message']);
$sender = $_POST['sender']; // admin 或 customer

/* 写入聊天记录 */
mysqli_query($conn,"
    INSERT INTO feedback_chat(feedback_id,sender,message,created_at)
    VALUES($fid,'$sender','$msg',NOW())
");

/* 回到聊天页面 */
header("Location: chat_feedback.php?id=$fid");
exit;
?>
