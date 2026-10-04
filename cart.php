<?php
require 'config.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// ---- handle cart actions (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $qty    = max(1, (int)($_POST['qty'] ?? 1));

    if ($action === 'add' && $id) {
        $s = $conn->prepare('SELECT stock FROM products WHERE id=?');
        $s->bind_param('i', $id); $s->execute();
        $row = $s->get_result()->fetch_assoc();
        if ($row && $row['stock'] > 0) {
            $new = ($_SESSION['cart'][$id] ?? 0) + $qty;
            $_SESSION['cart'][$id] = min($new, (int)$row['stock']);
            flash('Added to cart.');
        } else {
            flash('Product unavailable.', 'error');
        }
        redirect($_SERVER['HTTP_REFERER'] ?? 'cart.php');
    }
    if ($action === 'update' && $id) {
        $_SESSION['cart'][$id] = $qty;
        flash('Cart updated.');
    }
    if ($action === 'remove' && $id) {
        unset($_SESSION['cart'][$id]);
        flash('Item removed.');
    }
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        flash('Cart cleared.');
    }
    redirect('cart.php');
}

// ---- load cart items ----
$items = []; $total = 0;
if ($_SESSION['cart']) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($p = $res->fetch_assoc()) {
        $p['qty'] = min($_SESSION['cart'][$p['id']], max(1, (int)$p['stock']));
        $p['sub'] = $p['qty'] * $p['price'];
        $total += $p['sub'];
        $items[] = $p;
    }
}
$shipping = ($total > 0 && $total < 50) ? 5 : 0;

$title = 'Your Cart - VOGUEX';
include 'includes/header.php';
?>
<div class="container">
  <h1 class="page-title">Shopping Cart</h1>
  <?php if (!$items): ?>
    <p>Your cart is empty. <a href="shop.php"><u>Continue shopping</u></a></p>
  <?php else: ?>
  <div class="table-scroll">
  <table>
    <tr><th></th><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
    <?php foreach ($items as $p): ?>
    <tr>
      <td><img src="images/<?= e($p['image']) ?>" alt="" onerror="this.onerror=null;this.src='images/placeholder.jpg'"></td>
      <td><a href="product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a></td>
      <td><?= money($p['price']) ?></td>
      <td>
        <form method="post" class="inline-form">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <input type="number" name="qty" value="<?= (int)$p['qty'] ?>" min="1" max="<?= (int)$p['stock'] ?>" style="width:70px">
          <button class="btn btn-small btn-light" type="submit">Update</button>
        </form>
      </td>
      <td><?= money($p['sub']) ?></td>
      <td>
        <form method="post">
          <input type="hidden" name="action" value="remove">
          <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <button class="btn btn-small btn-danger" type="submit">Remove</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  </div>
  <div class="total-box">
    Subtotal: <?= money($total) ?><br>
    Shipping: <?= $shipping ? money($shipping) : 'FREE' ?><br>
    Total: <strong><?= money($total + $shipping) ?></strong>
  </div>
  <div class="btn-row" style="justify-content:flex-end">
    <form method="post"><input type="hidden" name="action" value="clear"><button class="btn btn-light" type="submit">CLEAR CART</button></form>
    <a href="shop.php" class="btn btn-light">CONTINUE SHOPPING</a>
    <a href="checkout.php" class="btn">CHECKOUT</a>
  </div>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
