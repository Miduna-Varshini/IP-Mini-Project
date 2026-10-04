<?php
include '_header.php';

// Delete a product (blocked if it already appears in an order)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $used = $conn->query("SELECT COUNT(*) c FROM order_items WHERE product_id=$id")->fetch_assoc()['c'];
    if ($used) {
        flash('Cannot delete: this product is part of existing orders. Set its stock to 0 instead.', 'error');
    } else {
        $conn->query("DELETE FROM products WHERE id=$id");
        flash('Product deleted.');
    }
    redirect('products.php');
}

$res = $conn->query('SELECT p.*, c.name AS cat FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC');
?>
<div class="toolbar">
  <h1 class="page-title" style="margin:28px 0 0">Products</h1>
  <a href="product_form.php" class="btn btn-small">+ ADD PRODUCT</a>
</div>
<div class="table-scroll"><table>
  <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
  <?php while ($p = $res->fetch_assoc()): ?>
  <tr>
    <td><img src="../images/<?= e($p['image']) ?>" alt="" onerror="this.onerror=null;this.src='../images/placeholder.jpg'"></td>
    <td><?= e($p['name']) ?></td>
    <td><?= e($p['cat']) ?></td>
    <td><?= money($p['price']) ?></td>
    <td><?= (int)$p['stock'] ?></td>
    <td>
      <a class="btn btn-small btn-light" href="product_form.php?id=<?= (int)$p['id'] ?>">Edit</a>
      <a class="btn btn-small btn-danger" href="products.php?delete=<?= (int)$p['id'] ?>" data-confirm="Delete this product?">Delete</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table></div>
<?php include '_footer.php'; ?>
