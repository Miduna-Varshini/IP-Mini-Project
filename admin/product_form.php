<?php
include '_header.php';
$id = (int)($_GET['id'] ?? 0);
$p  = ['name'=>'','description'=>'','price'=>'','old_price'=>'','image'=>'placeholder.jpg','badge'=>'','stock'=>50,'is_bestseller'=>0,'category_id'=>1];

if ($id) {
    $s = $conn->prepare('SELECT * FROM products WHERE id=?');
    $s->bind_param('i', $id); $s->execute();
    $p = $s->get_result()->fetch_assoc();
    if (!$p) { flash('Product not found.', 'error'); redirect('products.php'); }
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $cat   = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $old   = ($_POST['old_price'] ?? '') !== '' ? (float)$_POST['old_price'] : null;
    $badge = trim($_POST['badge'] ?? '') ?: null;
    $stock = max(0, (int)($_POST['stock'] ?? 0));
    $best  = isset($_POST['is_bestseller']) ? 1 : 0;
    $image = $p['image'];

    // optional image upload (jpg, png, webp)
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
            $error = 'Only JPG, PNG or WEBP images are allowed.';
        } else {
            $image = time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../images/' . $image);
        }
    }

    if (!$error && ($name === '' || $price <= 0 || !$cat)) $error = 'Name, category and a valid price are required.';

    if (!$error) {
        if ($id) {
            $st = $conn->prepare('UPDATE products SET category_id=?,name=?,description=?,price=?,old_price=?,image=?,badge=?,stock=?,is_bestseller=? WHERE id=?');
            $st->bind_param('issddssiii', $cat, $name, $desc, $price, $old, $image, $badge, $stock, $best, $id);
        } else {
            $st = $conn->prepare('INSERT INTO products (category_id,name,description,price,old_price,image,badge,stock,is_bestseller) VALUES (?,?,?,?,?,?,?,?,?)');
            $st->bind_param('issddssii', $cat, $name, $desc, $price, $old, $image, $badge, $stock, $best);
        }
        $st->execute();
        flash($id ? 'Product updated.' : 'Product added.');
        redirect('products.php');
    }
    $p = array_merge($p, ['name'=>$name,'description'=>$desc,'price'=>$price,'old_price'=>$old,'badge'=>$badge,'stock'=>$stock,'is_bestseller'=>$best,'category_id'=>$cat]);
}
$cats = $conn->query('SELECT * FROM categories ORDER BY id');
?>
<form method="post" enctype="multipart/form-data" class="form-box wide">
  <h2><?= $id ? 'Edit Product' : 'Add Product' ?></h2>
  <?php if ($error): ?><div class="flash error" style="margin:0 0 10px"><?= e($error) ?></div><?php endif; ?>
  <label>Name</label><input type="text" name="name" required value="<?= e($p['name']) ?>">
  <label>Description</label><textarea name="description" rows="3"><?= e($p['description']) ?></textarea>
  <label>Category</label>
  <select name="category_id">
    <?php while ($c = $cats->fetch_assoc()): ?>
      <option value="<?= (int)$c['id'] ?>" <?= $p['category_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
    <?php endwhile; ?>
  </select>
  <label>Price ($)</label><input type="number" step="0.01" name="price" required value="<?= e($p['price']) ?>">
  <label>Old Price ($) - optional, shows a strikethrough</label><input type="number" step="0.01" name="old_price" value="<?= e($p['old_price']) ?>">
  <label>Badge - optional (e.g. NEW or -20%)</label><input type="text" name="badge" maxlength="20" value="<?= e($p['badge']) ?>">
  <label>Stock</label><input type="number" name="stock" min="0" value="<?= (int)$p['stock'] ?>">
  <label><input type="checkbox" name="is_bestseller" <?= $p['is_bestseller']?'checked':'' ?>> Show in Best Sellers</label>
  <label>Image</label>
  <img id="preview" src="../images/<?= e($p['image']) ?>" alt="" style="width:120px;margin-bottom:8px" onerror="this.onerror=null;this.src='../images/placeholder.jpg'">
  <input type="file" name="image" id="imageInput" accept=".jpg,.jpeg,.png,.webp">
  <button class="btn" type="submit"><?= $id ? 'UPDATE PRODUCT' : 'ADD PRODUCT' ?></button>
  <p class="muted"><a href="products.php">Back to products</a></p>
</form>
<?php include '_footer.php'; ?>
