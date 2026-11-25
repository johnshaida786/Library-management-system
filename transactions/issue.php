<?php
require __DIR__ . '/../includes/header.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $bookId   = (int)($_POST['book_id'] ?? 0);
    $memberId = (int)($_POST['member_id'] ?? 0);
    $days     = max(1, min(60, (int)($_POST['days'] ?? LOAN_DAYS)));

    try {
        $pdo->beginTransaction();

        $b = $pdo->prepare('SELECT available_copies FROM books WHERE id=? FOR UPDATE');
        $b->execute([$bookId]);
        $book = $b->fetch();

        $m = $pdo->prepare('SELECT status FROM members WHERE id=?');
        $m->execute([$memberId]);
        $member = $m->fetch();

        $c = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE member_id=? AND status='issued'");
        $c->execute([$memberId]);
        $active = (int)$c->fetchColumn();

        if (!$book || $book['available_copies'] < 1) $errors[] = 'This book has no copies available.';
        if (!$member || $member['status'] !== 'active') $errors[] = 'Member is not active.';
        if ($active >= MAX_ACTIVE_LOANS) $errors[] = 'Member already has ' . MAX_ACTIVE_LOANS . ' books issued.';

        if (!$errors) {
            $pdo->prepare('INSERT INTO transactions (book_id, member_id, issue_date, due_date) VALUES (?,?,CURDATE(),DATE_ADD(CURDATE(), INTERVAL ? DAY))')
                ->execute([$bookId, $memberId, $days]);
            $pdo->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE id=?')->execute([$bookId]);
            $pdo->commit();
            flash('Book issued successfully.');
            redirect('/transactions/');
        }
        $pdo->rollBack();
    } catch (PDOException $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $errors[] = 'Database error: ' . $ex->getMessage();
    }
}

$books   = $pdo->query('SELECT id, title, author, available_copies FROM books WHERE available_copies > 0 ORDER BY title')->fetchAll();
$members = $pdo->query("SELECT id, name, email FROM members WHERE status='active' ORDER BY name")->fetchAll();
?>
<h1>Issue Book</h1>
<?php foreach ($errors as $er): ?><div class="alert error"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?>
  <label>Book</label>
  <select name="book_id" required>
    <option value="">— select —</option>
    <?php foreach ($books as $b): ?>
      <option value="<?= e($b['id']) ?>"><?= e($b['title']) ?> — <?= e($b['author']) ?> (<?= e($b['available_copies']) ?> left)</option>
    <?php endforeach; ?>
  </select>
  <label>Member</label>
  <select name="member_id" required>
    <option value="">— select —</option>
    <?php foreach ($members as $m): ?>
      <option value="<?= e($m['id']) ?>"><?= e($m['name']) ?> (<?= e($m['email']) ?>)</option>
    <?php endforeach; ?>
  </select>
  <label>Loan period (days)</label>
  <input type="number" name="days" min="1" max="60" value="<?= LOAN_DAYS ?>">
  <p><button class="btn green">Issue</button> <a class="btn gray" href="index.php">Cancel</a></p>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
