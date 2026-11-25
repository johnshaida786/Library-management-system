<?php
require __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? '');
$sql = 'SELECT * FROM books';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? OR category LIKE ?';
    $params = array_fill(0, 4, "%$q%");
}
$stmt = $pdo->prepare($sql . ' ORDER BY title');
$stmt->execute($params);
$books = $stmt->fetchAll();
?>
<h1>Books</h1>
<form class="toolbar" method="get">
  <input type="text" name="q" placeholder="Search title, author, ISBN, category" value="<?= e($q) ?>">
  <button class="btn">Search</button>
  <a class="btn green" href="form.php">+ Add Book</a>
</form>
<div class="table-wrap"><table>
<tr><th>ISBN</th><th>Title</th><th>Author</th><th>Category</th><th>Available / Total</th><th>Actions</th></tr>
<?php foreach ($books as $b): ?>
<tr>
  <td><?= e($b['isbn']) ?></td><td><?= e($b['title']) ?></td><td><?= e($b['author']) ?></td>
  <td><?= e($b['category']) ?></td><td><?= e($b['available_copies']) ?> / <?= e($b['total_copies']) ?></td>
  <td>
    <a class="btn small" href="form.php?id=<?= e($b['id']) ?>">Edit</a>
    <form class="inline" method="post" action="delete.php" onsubmit="return confirm('Delete this book?')">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($b['id']) ?>">
      <button class="btn small red">Delete</button>
    </form>
  </td>
</tr>
<?php endforeach; if (!$books): ?><tr><td colspan="6">No books found.</td></tr><?php endif; ?>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
