<?php
require __DIR__ . '/../includes/header.php';

$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$old = null;
if ($id) {
    $s = $pdo->prepare('SELECT * FROM books WHERE id=?');
    $s->execute([$id]);
    $old = $s->fetch();
    if (!$old) { flash('Book not found.', 'error'); redirect('/books/'); }
}
$book = $old ?: ['isbn' => '', 'title' => '', 'author' => '', 'category' => '', 'total_copies' => 1];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $book = [
        'isbn'         => trim($_POST['isbn'] ?? ''),
        'title'        => trim($_POST['title'] ?? ''),
        'author'       => trim($_POST['author'] ?? ''),
        'category'     => trim($_POST['category'] ?? ''),
        'total_copies' => (int)($_POST['total_copies'] ?? 0),
    ];
    if ($book['isbn'] === '')   $errors[] = 'ISBN is required.';
    if ($book['title'] === '')  $errors[] = 'Title is required.';
    if ($book['author'] === '') $errors[] = 'Author is required.';
    if ($book['total_copies'] < 1) $errors[] = 'Total copies must be at least 1.';

    $issuedNow = $old ? $old['total_copies'] - $old['available_copies'] : 0;
    if ($old && $book['total_copies'] < $issuedNow) {
        $errors[] = "$issuedNow copies are currently issued; total cannot be lower than that.";
    }

    if (!$errors) {
        try {
            if ($old) {
                $avail = $book['total_copies'] - $issuedNow;
                $pdo->prepare('UPDATE books SET isbn=?, title=?, author=?, category=?, total_copies=?, available_copies=? WHERE id=?')
                    ->execute([$book['isbn'], $book['title'], $book['author'], $book['category'], $book['total_copies'], $avail, $id]);
                flash('Book updated.');
            } else {
                $pdo->prepare('INSERT INTO books (isbn,title,author,category,total_copies,available_copies) VALUES (?,?,?,?,?,?)')
                    ->execute([$book['isbn'], $book['title'], $book['author'], $book['category'], $book['total_copies'], $book['total_copies']]);
                flash('Book added.');
            }
            redirect('/books/');
        } catch (PDOException $ex) {
            $errors[] = $ex->getCode() == 23000 ? 'A book with this ISBN already exists.' : 'Database error: ' . $ex->getMessage();
        }
    }
}
?>
<h1><?= $id ? 'Edit Book' : 'Add Book' ?></h1>
<?php foreach ($errors as $er): ?><div class="alert error"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <label>ISBN</label><input name="isbn" value="<?= e($book['isbn']) ?>" required>
  <label>Title</label><input name="title" value="<?= e($book['title']) ?>" required>
  <label>Author</label><input name="author" value="<?= e($book['author']) ?>" required>
  <label>Category</label><input name="category" value="<?= e($book['category']) ?>">
  <label>Total copies</label><input type="number" min="1" name="total_copies" value="<?= e($book['total_copies']) ?>" required>
  <p><button class="btn green">Save</button> <a class="btn gray" href="index.php">Cancel</a></p>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
