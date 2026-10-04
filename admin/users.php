<?php
include '_header.php';
$res = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id=u.id) AS order_count FROM users u ORDER BY u.id DESC");
?>
<h1 class="page-title">Customers</h1>
<div class="table-scroll"><table>
  <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Orders</th><th>Joined</th></tr>
  <?php while ($u = $res->fetch_assoc()): ?>
    <tr><td><?= (int)$u['id'] ?></td><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td>
        <td><?= e($u['role']) ?></td><td><?= (int)$u['order_count'] ?></td><td><?= e($u['created_at']) ?></td></tr>
  <?php endwhile; ?>
</table></div>
<?php include '_footer.php'; ?>
