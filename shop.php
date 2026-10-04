<?php
require 'config.php';
$title = 'Shop - VOGUEX';
$page  = 'shop';

$where = []; $types = ''; $vals = [];
$cat = (int)($_GET['cat'] ?? 0);
$q   = trim($_GET['q'] ?? '');
if ($cat)  { $where[] = 'category_id=?'; $types .= 'i'; $vals[] = $cat; }
if ($q !== '') { $where[] = '(name LIKE ? OR description LIKE ?)'; $types .= 'ss'; $like = "%$q%"; $vals[] = $like; $vals[] = $like; }
if (!empty($_GET['sale'])) $where[] = 'old_price IS NOT NULL';
if (!empty($_GET['new']))  $where[] = "badge='NEW'";

$sorts = ['new' => 'id DESC', 'low' => 'price ASC', 'high' => 'price DESC'];
$sort  = $_GET['sort'] ?? 'new';
$order = $sorts[$sort] ?? 'id DESC';

$sql  = 'SELECT * FROM products' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY $order";
$stmt = $conn->prepare($sql);
if ($vals) $stmt->bind_param($types, ...$vals);
$stmt->execute();
$res = $stmt->get_result();

$cats = $conn->query('SELECT * FROM categories ORDER BY id');
include 'includes/header.php';
?>
<div class="container">
  <h1 class="page-title"><?= !empty($_GET['sale']) ? 'Sale' : (!empty($_GET['new']) ? 'New Arrivals' : 'Shop All') ?></h1>
  <div class="toolbar">
    <div class="cats">
      <a href="shop.php" class="<?= !$cat ? 'on' : '' ?>">All</a>
      <?php while ($c = $cats->fetch_assoc()): ?>
        <a href="shop.php?cat=<?= (int)$c['id'] ?>" class="<?= $cat == $c['id'] ? 'on' : '' ?>"><?= e($c['name']) ?></a>
      <?php endwhile; ?>
    </div>
    <form method="get">
      <?php if ($cat): ?><input type="hidden" name="cat" value="<?= $cat ?>"><?php endif; ?>
      <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
      <select name="sort" onchange="this.form.submit()">
        <option value="new"  <?= $sort=='new'?'selected':'' ?>>Newest</option>
        <option value="low"  <?= $sort=='low'?'selected':'' ?>>Price: Low to High</option>
        <option value="high" <?= $sort=='high'?'selected':'' ?>>Price: High to Low</option>
      </select>
    </form>
  </div>

  <?php if ($res->num_rows): ?>
    <div class="grid"><?php while ($p = $res->fetch_assoc()) echo product_card($p); ?></div>
  <?php else: ?>
    <p style="margin:40px 0">No products found. <a href="shop.php"><u>View all products</u></a></p>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
