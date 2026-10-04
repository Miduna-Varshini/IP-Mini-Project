<?php
include '_header.php';

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid    = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['Pending','Shipped','Delivered','Cancelled'])) {
        $old = $conn->query("SELECT status FROM orders WHERE id=$oid")->fetch_assoc()['status'] ?? null;
        if ($old && $old !== $status) {
            // restore stock when an order becomes Cancelled
            if ($status === 'Cancelled') {
                $conn->query("UPDATE products p JOIN order_items oi ON oi.product_id=p.id SET p.stock=p.stock+oi.quantity WHERE oi.order_id=$oid");
            }
            $st = $conn->prepare('UPDATE orders SET status=? WHERE id=?');
            $st->bind_param('si', $status, $oid); $st->execute();
            flash("Order #$oid marked $status.");
        }
    }
    redirect('orders.php');
}
$res = $conn->query('SELECT o.*, u.name, u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC');
?>
<h1 class="page-title">Orders</h1>
<?php if (!$res->num_rows): ?><p>No orders yet.</p><?php endif; ?>
<?php while ($o = $res->fetch_assoc()): ?>
  <div class="order">
    <div class="order-head">
      <div><b>Order #<?= (int)$o['id'] ?></b> &middot; <?= e($o['name']) ?> (<?= e($o['email']) ?>) &middot; <?= e($o['created_at']) ?></div>
      <form method="post" class="inline-form">
        <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
        <select name="status">
          <?php foreach (['Pending','Shipped','Delivered','Cancelled'] as $s): ?>
            <option <?= $o['status']==$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-small" type="submit">Save</button>
      </form>
    </div>
    <p>Items:
    <?php
    $it = $conn->query('SELECT oi.quantity, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=' . (int)$o['id']);
    $list = [];
    while ($r = $it->fetch_assoc()) $list[] = e($r['name']) . ' x' . (int)$r['quantity'];
    echo implode(', ', $list);
    ?></p>
    <p>Total: <b><?= money($o['total']) ?></b> &middot; Phone: <?= e($o['phone']) ?> &middot; Address: <?= e($o['address']) ?></p>
  </div>
<?php endwhile; ?>
<?php include '_footer.php'; ?>
