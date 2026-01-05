<?php
session_start();
require "config/db.php";

/* Must login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$message = "";

/* Fetch user info */
$user = mysqli_fetch_assoc(mysqli_query(
    $mysqli,
    "SELECT username, email, avatar, balance FROM users WHERE id = $user_id LIMIT 1"
));

if (!$user) {
    die("User not found.");
}

/* ======================
   UPDATE AVATAR
====================== */
if (isset($_POST['update_avatar']) && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {

    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($ext, $allowed)) {

        $filename = "avatar_" . $user_id . "_" . time() . "." . $ext;

        // ✅ 改 1：磁盘真实路径（Render / 本地都稳定）
        $target = __DIR__ . "/assets/avatars/" . $filename;

        if (!is_dir(__DIR__ . "/assets/avatars")) {
            mkdir(__DIR__ . "/assets/avatars", 0777, true);
        }

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            mysqli_query($mysqli, "UPDATE users SET avatar='$filename' WHERE id=$user_id");
            $user['avatar'] = $filename;
            $message = "Avatar updated successfully.";
        }
    }
}

/* ======================
   CHANGE PASSWORD
====================== */
if (isset($_POST['change_password']) && !empty($_POST['new_password'])) {

    $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    mysqli_query($mysqli, "UPDATE users SET password_hash='$hash' WHERE id=$user_id");

    $message = "Password updated successfully.";
}

/* ======================
   Avatar display path
====================== */

// ✅ 改 2：浏览器用「网站根目录绝对路径」
$avatar = $user['avatar']
    ? "/assets/avatars/" . htmlspecialchars($user['avatar'])
    : "/assets/avatars/default.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>

<style>
body{
  margin:0;
  height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  font-family:Segoe UI, Arial;
  background:
    linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
    url("/assets/img/bg-orderingol.jpg") center / cover no-repeat;
}

.card{
  width:420px;
  background:#111827;
  padding:30px;
  border-radius:20px;
  color:white;
  box-shadow:0 30px 60px rgba(0,0,0,.6);
  text-align:center;
}

.avatar{
  width:120px;
  height:120px;
  border-radius:50%;
  object-fit:cover;
  border:4px solid #17C3B2;
  margin-bottom:14px;
}

h2{margin:8px 0 4px;}
p{margin:0;color:#9ca3af;font-size:14px;}

.balance{
  margin:16px 0;
  font-size:18px;
  font-weight:600;
  color:#FDE68A;
}

form{margin-top:18px;}

input[type=password]{
  width:100%;
  padding:10px;
  border-radius:10px;
  border:none;
  margin-bottom:12px;
}

button{
  width:100%;
  padding:10px;
  border:none;
  border-radius:12px;
  background:#17C3B2;
  color:white;
  font-weight:600;
  cursor:pointer;
}
button:hover{background:#0fa399;}

.upload-label{
  display:block;
  background:#1f2937;
  padding:10px;
  border-radius:10px;
  cursor:pointer;
  margin-bottom:10px;
}
.upload-label:hover{background:#374151;}

a{
  display:block;
  margin-top:18px;
  color:#38bdf8;
  text-decoration:none;
}

.msg{
  margin-top:12px;
  color:#22c55e;
  font-size:14px;
}
</style>
</head>

<body>

<div class="card">

  <!-- ✅ 改 3：头像一定能显示 -->
  <img src="<?= $avatar ?>" class="avatar" alt="Avatar">

  <h2><?= htmlspecialchars($user['username']) ?></h2>
  <p><?= htmlspecialchars($user['email']) ?></p>

  <div class="balance">
    Balance: RM <?= number_format($user['balance'], 2) ?>
  </div>

  <!-- Update Avatar -->
  <form method="post" enctype="multipart/form-data">
    <label class="upload-label">
      Select Avatar
      <input type="file" name="avatar" accept="image/*" hidden>
    </label>
    <button name="update_avatar">Update Avatar</button>
  </form>

  <!-- Change Password -->
  <form method="post">
    <input type="password" name="new_password" placeholder="New Password" required>
    <button name="change_password">Change Password</button>
  </form>

  <?php if ($message): ?>
    <div class="msg"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <a href="menu.php">← Back to Menu</a>

</div>

</body>
</html>
