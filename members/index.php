<?php
require __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? '');
$sql = 'SELECT m.*, (SELECT COUNT(*) FROM transactions t WHERE t.member_id=m.id AND t.status="issued") AS active_loans FROM members m';
$params = [];
if ($q !== '') { $sql .= ' WHERE m.name LIKE ? OR m.email LIKE ? OR m.phone LIKE ?'; $params = array_fill(0, 3, "%$q%"); }
$stmt = $pdo->prepare($sql . ' ORDER BY m.name');
$stmt->execute($params);
$members = $stmt->fetchAll();
?>
<h1>Members</h1>
<form class="toolbar" method="get">
  <input type="text" name="q" placeholder="Search name, email, phone" value="<?= e($q) ?>">
  <button class="btn">Search</button>
  <a class="btn green" href="form.php">+ Add Member</a>
</form>
<div class="table-wrap"><table>
<tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Books out</th><th>Actions</th></tr>
<?php foreach ($members as $m): ?>
<tr>
  <td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td><td><?= e($m['phone']) ?></td>
  <td><span class="badge <?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
  <td><?= e($m['active_loans']) ?></td>
  <td>
    <a class="btn small" href="form.php?id=<?= e($m['id']) ?>">Edit</a>
    <form class="inline" method="post" action="delete.php" onsubmit="return confirm('Delete this member?')">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($m['id']) ?>">
      <button class="btn small red">Delete</button>
    </form>
  </td>
</tr>
<?php endforeach; if (!$members): ?><tr><td colspan="6">No members found.</td></tr><?php endif; ?>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
