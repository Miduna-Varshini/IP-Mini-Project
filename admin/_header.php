<?php
// Admin header: loads config, blocks non-admins
require __DIR__ . '/../config.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - VOGUEX</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-bar">
  <b>VOGUEX ADMIN</b>
  <a href="index.php">Dashboard</a>
  <a href="products.php">Products</a>
  <a href="orders.php">Orders</a>
  <a href="users.php">Customers</a>
  <a href="../index.php" style="margin-left:auto">View Store</a>
  <a href="../logout.php">Logout</a>
</div>
<div class="container"><?php show_flash(); ?>
