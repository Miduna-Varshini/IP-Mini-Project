<?php
require 'config.php';
require_login();
if (empty($_SESSION['cart'])) { flash('Your cart is empty.', 'error'); redirect('cart.php'); }

// load cart
$ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
$res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
$items = []; $total = 0;
while ($p = $res->fetch_assoc()) {
    $p['qty'] = $_SESSION['cart'][$p['id']];
    $total += $p['qty'] * $p['price'];
    $items[] = $p;
}
$shipping = $total < 50 ? 5 : 0;
$grand = $total + $shipping;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    if ($address === '' || !preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Enter a valid address and 10-digit phone number.';
    } else {
        $conn->begin_transaction();
        $ok = true;
        $st = $conn->prepare('INSERT INTO orders (user_id,total,address,phone) VALUES (?,?,?,?)');
        $st->bind_param('idss', $_SESSION['user_id'], $grand, $address, $phone);
        $ok = $st->execute();
        $order_id = $conn->insert_id;

        foreach ($items as $p) {
            if (!$ok) break;
            $u = $conn->prepare('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?');
            $u->bind_param('iii', $p['qty'], $p['id'], $p['qty']);
            $u->execute();
            if ($u->affected_rows !== 1) { $ok = false; $error = 'Not enough stock for ' . $p['name'] . '.'; break; }
            $i = $conn->prepare('INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)');
            $i->bind_param('iiid', $order_id, $p['id'], $p['qty'], $p['price']);
            $ok = $i->execute();
        }
        if ($ok) {
            $conn->commit();
            $_SESSION['cart'] = [];
            flash("Order #$order_id placed successfully! Thank you for shopping.");
            redirect('orders.php');
        } else {
            $conn->rollback();
            if (!$error) $error = 'Could not place order. Try again.';
        }
    }
}

$title = 'Checkout - VOGUEX';
include 'includes/header.php';
?>
<div class="container">
  <h1 class="page-title">Checkout</h1>
  <?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
  <div class="table-scroll">
  <table>
    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
    <?php foreach ($items as $p): ?>
      <tr><td><?= e($p['name']) ?></td><td><?= (int)$p['qty'] ?></td><td><?= money($p['price']) ?></td><td><?= money($p['qty'] * $p['price']) ?></td></tr>
    <?php endforeach; ?>
  </table>
  </div>
  <div class="total-box">Shipping: <?= $shipping ? money($shipping) : 'FREE' ?><br>Total: <strong><?= money($grand) ?></strong></div>

  <form method="post" id="checkoutForm" class="form-box wide" style="margin-top:0">
    <h2>Delivery Details</h2>
    <label>Name</label>
    <input type="text" value="<?= e($_SESSION['name']) ?>" disabled>
    <label>Delivery Address</label>
    <textarea name="address" rows="3" required><?= e($_POST['address'] ?? '') ?></textarea>
    <label>Phone (10 digits)</label>
    <input type="tel" name="phone" maxlength="10" required value="<?= e($_POST['phone'] ?? '') ?>">
    <label>Payment</label>
    <input type="text" value="Cash on Delivery" disabled>
    <button class="btn" type="submit">PLACE ORDER</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
