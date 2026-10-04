<?php
require 'config.php';
$id   = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT p.*, c.name AS cat FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { flash('Product not found.', 'error'); redirect('shop.php'); }

$title = $p['name'] . ' - VOGUEX';
$page  = 'shop';
$rel   = $conn->query('SELECT * FROM products WHERE category_id=' . (int)$p['category_id'] . ' AND id<>' . $id . ' LIMIT 4');
include 'includes/header.php';
?>
<div class="container">
  <div class="detail">
    <div class="big"><img src="images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" onerror="this.onerror=null;this.src='images/placeholder.jpg'"></div>
    <div>
      <small><?= e($p['cat']) ?></small>
      <h1><?= e($p['name']) ?></h1>
      <p class="price"><?php if ($p['old_price']): ?><span class="old"><?= money($p['old_price']) ?></span><?php endif; ?><strong><?= money($p['price']) ?></strong></p>
      <p><?= nl2br(e($p['description'])) ?></p>
      <p><?= $p['stock'] > 0 ? 'In stock: ' . (int)$p['stock'] . ' available' : '<span class="out">Out of stock</span>' ?></p>
      <?php if ($p['stock'] > 0): ?>
      <form method="post" action="cart.php" style="display:flex">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="number" name="qty" class="qty" value="1" min="1" max="<?= (int)$p['stock'] ?>">
        <button class="btn" type="submit">ADD TO CART</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($rel->num_rows): ?>
    <h2 class="section-title">YOU MAY ALSO LIKE</h2>
    <div class="grid"><?php while ($r = $rel->fetch_assoc()) echo product_card($r); ?></div>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
