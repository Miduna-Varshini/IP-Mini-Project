<?php
// config.php - database connection + common helpers (included on every page)

session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';      // XAMPP default
$DB_PASS = '';          // XAMPP default (empty)
$DB_NAME = 'voguex_db';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error .
        '<br>Did you import database.sql in phpMyAdmin?');
}
$conn->set_charset('utf8mb4');

// ---------- helpers ----------
function e($text) { return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8'); }
function money($n) { return '$' . number_format((float)$n, 2); }
function is_logged_in() { return isset($_SESSION['user_id']); }
function is_admin() { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function redirect($url) { header("Location: $url"); exit; }

function require_login() {
    if (!is_logged_in()) {
        flash('Please login to continue.', 'error');
        redirect('login.php');
    }
}
function require_admin() {
    if (!is_admin()) {
        flash('Admin access only.', 'error');
        redirect('../login.php');
    }
}

function cart_count() {
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}

// Flash messages (shown once)
function flash($msg, $type = 'success') { $_SESSION['flash'] = [$msg, $type]; }
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        [$msg, $type] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="flash ' . e($type) . '">' . e($msg) . '</div>';
    }
}

// Product card used on home, shop and product pages
function product_card($p) {
    $badge = '';
    if (!empty($p['badge'])) {
        $cls = ($p['badge'][0] === '-') ? 'sale' : 'new';
        $badge = '<span class="badge ' . $cls . '">' . e($p['badge']) . '</span>';
    }
    $old = $p['old_price'] ? '<span class="old">' . money($p['old_price']) . '</span>' : '';
    $stock = $p['stock'] > 0
        ? '<form method="post" action="cart.php">
             <input type="hidden" name="action" value="add">
             <input type="hidden" name="id" value="' . (int)$p['id'] . '">
             <button class="btn btn-small" type="submit">Add to Cart</button>
           </form>'
        : '<span class="out">Out of stock</span>';
    return '<div class="card">' . $badge . '
      <a href="product.php?id=' . (int)$p['id'] . '" class="card-img">
        <img src="images/' . e($p['image']) . '" alt="' . e($p['name']) . '"
             onerror="this.onerror=null;this.src=\'images/placeholder.jpg\'">
      </a>
      <h3><a href="product.php?id=' . (int)$p['id'] . '">' . e($p['name']) . '</a></h3>
      <p class="price">' . $old . '<strong>' . money($p['price']) . '</strong></p>
      ' . $stock . '
    </div>';
}
