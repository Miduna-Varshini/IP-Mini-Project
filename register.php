<?php
require 'config.php';
if (is_logged_in()) redirect('index.php');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $conf  = $_POST['confirm'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Enter a valid name and email.';
    elseif (strlen($pass) < 6) $error = 'Password must be at least 6 characters.';
    elseif ($pass !== $conf)   $error = 'Passwords do not match.';
    else {
        $s = $conn->prepare('SELECT id FROM users WHERE email=?');
        $s->bind_param('s', $email); $s->execute();
        if ($s->get_result()->num_rows) {
            $error = 'This email is already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $i = $conn->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
            $i->bind_param('sss', $name, $email, $hash);
            $i->execute();
            session_regenerate_id(true);
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['name']    = $name;
            $_SESSION['role']    = 'customer';
            flash('Welcome to VOGUEX, ' . $name . '!');
            redirect('index.php');
        }
    }
}
$title = 'Register - VOGUEX';
include 'includes/header.php';
?>
<div class="container">
  <form method="post" id="registerForm" class="form-box">
    <h2>Create Account</h2>
    <?php if ($error): ?><div class="flash error" style="margin:0 0 10px"><?= e($error) ?></div><?php endif; ?>
    <label>Full Name</label><input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
    <label>Email</label><input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
    <label>Password</label><input type="password" name="password" required>
    <label>Confirm Password</label><input type="password" name="confirm" required>
    <button class="btn" type="submit">REGISTER</button>
    <p class="muted">Already have an account? <a href="login.php">Login</a></p>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
