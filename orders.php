<?php
require 'config.php';
require_login();
$uid = $_SESSION['user_id'];

// Cancel a pending order (restores stock)
if (isset($_GET['cancel'])) {
    $oid = (int)$_GET['cancel'];
    $s = $conn->prepare("SELECT id FROM orders WHERE id=? AND user_id=? AND status='Pending'");
    $s->bind_param('ii', $oid, $uid); $s->execute();
    if ($s->get_result()->num_rows) {
        $conn->query("UPDATE products p JOIN order_items oi ON oi.product_id=p.id SET p.stock=p.stock+oi.quantity WHERE oi.order_id=$oid");
        $conn->query("UPDATE orders SET status='Cancelled' WHERE id=$oid");
        flash("Order #$oid cancelled.");
    }
    redirect('orders.php');
}

$s = $conn->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC');
$s->bind_param('i', $uid); $s->execute();
$orders = $s->get_result();

$title = 'My Orders - VOGUEX';
include 'includes/header.php';
?>
<div class="container">
  <h1 class="page-title">My Orders</h1>
  <?php if (!$orders->num_rows): ?>
    <p>You have no orders yet. <a href="shop.php"><u>Start shopping</u></a></p>
  <?php endif; ?>
  <?php while ($o = $orders->fetch_assoc()): ?>
    <div class="order">
      <div class="order-head">
        <div><b>Order #<?= (int)$o['id'] ?></b> &middot; <?= e(date('d M Y, h:i A', strtotime($o['created_at']))) ?></div>
        <div><span class="status <?= e($o['status']) ?>"><?= e($o['status']) ?></span></div>
      </div>
      <div class="table-scroll">
      <table>
        <tr><th>Product</th><th>Qty</th><th>Price</th></tr>
        <?php
        $it = $conn->query('SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=' . (int)$o['id']);
        while ($r = $it->fetch_assoc()): ?>
          <tr><td><?= e($r['name']) ?></td><td><?= (int)$r['quantity'] ?></td><td><?= money($r['price']) ?></td></tr>
        <?php endwhile; ?>
      </table>
      </div>
      <p>Total: <b><?= money($o['total']) ?></b> &middot; Deliver to: <?= e($o['address']) ?></p>
      <?php if ($o['status'] === 'Pending'): ?>
        <p style="margin-top:10px"><a class="btn btn-small btn-danger" href="orders.php?cancel=<?= (int)$o['id'] ?>" data-confirm="Cancel this order?">Cancel Order</a></p>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>
