<?php
require 'config.php';
if (is_logged_in()) redirect('index.php');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $s = $conn->prepare('SELECT * FROM users WHERE email=?');
    $s->bind_param('s', $email); $s->execute();
    $u = $s->get_result()->fetch_assoc();

    if ($u && password_verify($pass, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['name']    = $u['name'];
        $_SESSION['role']    = $u['role'];
        flash('Welcome back, ' . $u['name'] . '!');
        redirect($u['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }
    $error = 'Invalid email or password.';
}
$title = 'Login - VOGUEX';
include 'includes/header.php';
?>
<div class="container">
  <form method="post" class="form-box">
    <h2>Login</h2>
    <?php if ($error): ?><div class="flash error" style="margin:0 0 10px"><?= e($error) ?></div><?php endif; ?>
    <label>Email</label><input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
    <label>Password</label><input type="password" name="password" required>
    <button class="btn" type="submit">LOGIN</button>
    <p class="muted">New to VOGUEX? <a href="register.php">Create an account</a></p>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
