<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["login"]);
    $password = $_POST["password"];

    if ($login === "" || $password === "") {
        $error = "Please enter username/email and password.";
    } else {

        // username OR email login
        $stmt = $conn->prepare(
            "SELECT id, username, password_hash, role 
             FROM users 
             WHERE username = ? OR email = ? 
             LIMIT 1"
        );

        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password_hash"])) {

                // login success → SET SESSION
                $_SESSION["user_id"]  = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"]     = $user["role"];

                /* ⬇⬇⬇ 登录后根据角色跳转 (核心修复点) ⬇⬇⬇ */
                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php"); // 🔥 你指定的管理主页
                } else {
                    header("Location: menu.php"); // 🔥 Customer跳转
                }
                exit();

            } else {
                $error = "Invalid password.";
            }

        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>orderingol | Login</title>

<style>
:root{
  --primary-yellow:#F4E04D;
  --accent-teal:#17C3B2;
  --light:#6B7280;
}

*{ box-sizing:border-box; font-family:Segoe UI, Arial;}

body{
  margin:0;min-height:100vh;
  background:url("assets/img/bg-orderingol.jpg") center/cover no-repeat;
  display:flex;justify-content:center;align-items:center;
}

.login-box{
  background:var(--primary-yellow);
  width:320px;padding:26px;border-radius:18px;
  box-shadow:0 12px 30px rgba(0,0,0,.25);
}

.app-title{text-align:center;font-size:26px;font-weight:700;}
.subtitle{text-align:center;font-size:13px;color:var(--light);margin-bottom:18px;}

input{
  width:100%;padding:12px;margin-bottom:12px;font-size:14px;
  border:none;border-radius:10px;
}

button{
  width:100%;padding:12px;background:var(--accent-teal);color:white;
  border:none;border-radius:10px;font-size:15px;cursor:pointer;
}
.error{color:red;font-size:13px;text-align:center;margin-bottom:10px;}
</style>
</head>

<body>
<div class="login-box">
    <div class="app-title">orderingol</div>
    <div class="subtitle">Smart Food Ordering Platform</div>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="login" placeholder="Username or Email">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
