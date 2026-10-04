<?php
// Shared header. Set $title and $page before including.
$title = $title ?? 'VOGUEX';
$page  = $page ?? '';
$cats  = $conn->query("SELECT id, name FROM categories ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="topbar">
  <span>Free shipping on orders over $50</span>
  <span>New here? Get 10% off your first order | Code: <b>WELCOME10</b></span>
  <span>
    <?php if (is_admin()): ?><a href="admin/index.php">Admin Panel</a><?php endif; ?>
    <a href="orders.php">Track Order</a>
  </span>
</div>

<header class="header">
  <div class="container nav-wrap">
    <a href="index.php" class="logo">VOGUE<span>X</span><small>FASHION REDEFINED</small></a>
    <button class="menu-btn" id="menuBtn" aria-label="Menu">&#9776;</button>
    <nav id="mainNav">
      <a href="index.php" class="<?= $page=='home'?'active':'' ?>">HOME</a>
      <a href="shop.php" class="<?= $page=='shop'?'active':'' ?>">SHOP</a>
      <a href="shop.php?new=1">NEW ARRIVALS</a>
      <?php while ($c = $cats->fetch_assoc()): ?>
        <a href="shop.php?cat=<?= (int)$c['id'] ?>"><?= strtoupper(e($c['name'])) ?></a>
      <?php endwhile; ?>
      <a href="shop.php?sale=1" class="sale-link">SALE</a>
    </nav>
    <div class="icons">
      <form action="shop.php" method="get" class="search">
        <input type="text" name="q" placeholder="Search products..." value="<?= e($_GET['q'] ?? '') ?>">
      </form>
      <?php if (is_logged_in()): ?>
        <a href="orders.php" title="My orders">Hi, <?= e(explode(' ', $_SESSION['name'])[0]) ?></a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
      <a href="cart.php" class="cart-link">Cart <span class="cart-count"><?= cart_count() ?></span></a>
    </div>
  </div>
</header>
<main>
<div class="container"><?php show_flash(); ?></div>
