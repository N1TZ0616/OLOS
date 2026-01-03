<?php
session_start();
require "config/db.php";

$error = "";

/* ======================
   LOGIN
====================== */
if (
    isset($_POST['login']) &&
    !empty($_POST['login_input']) &&
    !empty($_POST['password'])
) {

    // ✅ 使用 $mysqli（云端 + 本地都兼容）
    $login_input = mysqli_real_escape_string($mysqli, $_POST['login_input']);
    $password    = $_POST['password'];

    $sql = "SELECT * FROM users 
            WHERE username='$login_input' 
               OR email='$login_input'
            LIMIT 1";

    $result = mysqli_query($mysqli, $sql);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password_hash'])) {

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            /* ===== Role redirect ===== */
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] === 'restaurant_staff') {
                header("Location: staff/dashboard.php");
            } elseif ($user['role'] === 'delivery_driver') {
                header("Location: delivery/dashboard.php");
            } else {
                header("Location: menu.php");
            }
            exit;

        } else {
            $error = "Invalid password.";
        }

    } else {
        $error = "User not found.";
    }
}

/* ======================
   REGISTER
====================== */
if (
    isset($_POST['register']) &&
    !empty($_POST['username']) &&
    !empty($_POST['email']) &&
    !empty($_POST['password']) &&
    !empty($_POST['role'])
) {

    // ✅ 使用 $mysqli
    $username = mysqli_real_escape_string($mysqli, $_POST['username']);
    $email    = mysqli_real_escape_string($mysqli, $_POST['email']);
    $role     = $_POST['role'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query(
        $mysqli,
        "SELECT id FROM users 
         WHERE username='$username' OR email='$email'"
    );

    if ($check && mysqli_num_rows($check) > 0) {
        $error = "Username or Email already exists.";
    } else {

        $sql = "INSERT INTO users (username, email, password_hash, role)
                VALUES ('$username','$email','$password_hash','$role')";

        if (mysqli_query($mysqli, $sql)) {
            $error = "Registration successful. Please login.";
        } else {
            $error = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>orderingol | Login</title>

<style>
body{
  margin:0;
  height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  font-family:Segoe UI, Arial;

  background:
    linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)),
    url("assets/img/bg-orderingol.jpg") center / cover no-repeat;
}

.container{
  width:820px;
  display:flex;
  background:rgba(255,255,255,.95);
  border-radius:20px;
  overflow:hidden;
  box-shadow:0 25px 50px rgba(0,0,0,.35);
}

.left{
  width:50%;
  background:#F4E04D;
  padding:50px;
}

.right{
  width:50%;
  padding:40px;
}

h1{ margin:0; }
h2{ margin-top:0; }

input, select, button{
  width:100%;
  padding:10px;
  margin-bottom:12px;
}

button{
  background:#17C3B2;
  color:white;
  border:none;
  cursor:pointer;
}

.error{
  color:red;
  font-size:14px;
  margin-bottom:10px;
}

.switch{
  cursor:pointer;
  color:#17C3B2;
  font-size:14px;
}
</style>

<script>
function showRegister(){
  document.getElementById("loginBox").style.display="none";
  document.getElementById("registerBox").style.display="block";
}
function showLogin(){
  document.getElementById("registerBox").style.display="none";
  document.getElementById("loginBox").style.display="block";
}
</script>

</head>
<body>

<div class="container">

  <div class="left">
    <h1>orderingol</h1>
    <p>Smart Food Ordering Platform</p>
  </div>

  <div class="right">

    <?php if ($error !== ""): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- LOGIN -->
    <div id="loginBox">
      <h2>Login</h2>
      <form method="post">
        <input type="text" name="login_input" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
      </form>
      <div class="switch" onclick="showRegister()">No account? Register</div>
    </div>

    <!-- REGISTER -->
    <div id="registerBox" style="display:none;">
      <h2>Register</h2>
      <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
          <option value="customer">Customer</option>
          <option value="admin">Admin</option>
          <option value="restaurant_staff">Restaurant Staff</option>
          <option value="delivery_driver">Delivery</option>
        </select>

        <button type="submit" name="register">Register</button>
      </form>
      <div class="switch" onclick="showLogin()">Back to Login</div>
    </div>

  </div>

</div>

</body>
</html>
