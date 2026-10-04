<?php
include '_header.php';
$users    = $conn->query("SELECT COUNT(*) c FROM users WHERE role='customer'")->fetch_assoc()['c'];
$products = $conn->query('SELECT COUNT(*) c FROM products')->fetch_assoc()['c'];
$orders   = $conn->query('SELECT COUNT(*) c FROM orders')->fetch_assoc()['c'];
$revenue  = $conn->query("SELECT COALESCE(SUM(total),0) s FROM orders WHERE status<>'Cancelled'")->fetch_assoc()['s'];
$recent   = $conn->query('SELECT o.*, u.name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 5');
$low      = $conn->query('SELECT name, stock FROM products WHERE stock<=10 ORDER BY stock LIMIT 5');
?>
<h1 class="page-title">Dashboard</h1>
<div class="stats">
  <div class="stat"><b><?= (int)$users ?></b>Customers</div>
  <div class="stat"><b><?= (int)$products ?></b>Products</div>
  <div class="stat"><b><?= (int)$orders ?></b>Orders</div>
  <div class="stat"><b><?= money($revenue) ?></b>Revenue</div>
</div>

<h2>Recent Orders</h2><br>
<div class="table-scroll"><table>
  <tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
  <?php while ($o = $recent->fetch_assoc()): ?>
    <tr><td>#<?= (int)$o['id'] ?></td><td><?= e($o['name']) ?></td><td><?= money($o['total']) ?></td>
        <td><span class="status <?= e($o['status']) ?>"><?= e($o['status']) ?></span></td><td><?= e($o['created_at']) ?></td></tr>
  <?php endwhile; ?>
</table></div>

<h2>Low Stock (10 or fewer)</h2><br>
<table>
  <tr><th>Product</th><th>Stock</th></tr>
  <?php if (!$low->num_rows): ?><tr><td colspan="2">All products are well stocked.</td></tr><?php endif; ?>
  <?php while ($l = $low->fetch_assoc()): ?>
    <tr><td><?= e($l['name']) ?></td><td><?= (int)$l['stock'] ?></td></tr>
  <?php endwhile; ?>
</table>
<?php include '_footer.php'; ?>
