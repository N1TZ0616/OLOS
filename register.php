<?php
// register.php
// Create new user. Stores password using password_hash.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db_connect.php';

$errors = [];
$success = '';
$username = $full_name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($username === '') $errors[] = 'Username is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($password === '' || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Password confirmation does not match.';

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO `users` (`username`,`password_hash`,`full_name`,`email`,`role`) VALUES (?,?,?,?,?)");
        if ($stmt) {
            $role = 'customer';
            $stmt->bind_param('sssss', $username, $hash, $full_name, $email, $role);
            if ($stmt->execute()) {
                $success = 'Registration successful. You may login now.';
                $username = $full_name = $email = '';
            } else {
                $errors[] = 'Registration failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = 'Service temporarily unavailable.';
            error_log('Register prepare error: ' . $mysqli->error);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register — Food Delivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--bg:#f0e0df;--card:#f6e34b;--accent:#14c0bf;--muted:#6b7280;--maxw:420px;}
html,body{margin:0;height:100%;font-family:Arial,Helvetica,sans-serif;background:var(--bg);color:#111;}
.container{max-width:var(--maxw);margin:24px auto;padding:12px;}
.header{background:var(--card);padding:16px;border-radius:12px;text-align:center;font-weight:800;}
.card{background:var(--card);padding:14px;border-radius:12px;margin-top:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);}
.label{display:block;margin-top:10px;font-weight:700;}
.input{width:100%;padding:10px;border-radius:10px;border:1px solid #eee;margin-top:8px;font-size:14px;}
.button{background:var(--accent);color:#fff;padding:12px;border-radius:12px;border:none;margin-top:14px;font-weight:800;width:100%;cursor:pointer;}
.message-success{background:#ecfdf5;color:#065f46;padding:10px;border-radius:8px;margin-bottom:10px;}
.message-error{background:#fde2e2;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:10px;}
.small{display:block;text-align:center;margin-top:10px;color:#0f172a;text-decoration:none;font-weight:700;}
.footer{margin-top:18px;text-align:center;color:var(--muted);font-size:13px;}
</style>
</head>
<body>
  <div class="container">
    <div class="header">Food Delivery</div>

    <div class="card">
      <h2 style="margin:0 0 8px 0;">Create Account</h2>

      <?php if (!empty($errors)): ?>
        <div class="message-error"><ul style="margin:0;padding-left:18px;"><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="message-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form method="post" action="register.php" novalidate>
        <label class="label" for="username">Username</label>
        <input id="username" name="username" class="input" type="text" value="<?php echo htmlspecialchars($username); ?>">

        <label class="label" for="full_name">Full name</label>
        <input id="full_name" name="full_name" class="input" type="text" value="<?php echo htmlspecialchars($full_name); ?>">

        <label class="label" for="email">Email</label>
        <input id="email" name="email" class="input" type="email" value="<?php echo htmlspecialchars($email); ?>">

        <label class="label" for="password">Password</label>
        <input id="password" name="password" class="input" type="password">

        <label class="label" for="confirm">Confirm password</label>
        <input id="confirm" name="confirm" class="input" type="password">

        <button class="button" type="submit">Register</button>
      </form>

      <a class="small" href="index.php">Already have an account? Login</a>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Food Delivery</div>
  </div>
</body>
</html>
