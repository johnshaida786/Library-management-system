<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/transactions/');
csrf_check();
$id = (int)($_POST['id'] ?? 0);

try {
    $pdo->beginTransaction();
    $s = $pdo->prepare("SELECT * FROM transactions WHERE id=? AND status='issued' FOR UPDATE");
    $s->execute([$id]);
    $t = $s->fetch();

    if (!$t) {
        $pdo->rollBack();
        flash('Transaction not found or already returned.', 'error');
    } else {
        $daysLate = max(0, (int)floor((strtotime(date('Y-m-d')) - strtotime($t['due_date'])) / 86400));
        $fine = $daysLate * FINE_PER_DAY;
        $pdo->prepare("UPDATE transactions SET status='returned', return_date=CURDATE(), fine=? WHERE id=?")->execute([$fine, $id]);
        $pdo->prepare('UPDATE books SET available_copies = available_copies + 1 WHERE id=?')->execute([$t['book_id']]);
        $pdo->commit();
        flash($fine > 0 ? "Book returned. Fine due: ₹$fine ($daysLate days late)." : 'Book returned on time.');
    }
} catch (PDOException $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('Database error: ' . $ex->getMessage(), 'error');
}
redirect('/transactions/');
