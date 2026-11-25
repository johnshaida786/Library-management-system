<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/books/');
csrf_check();
$id = (int)($_POST['id'] ?? 0);

$c = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE book_id=?');
$c->execute([$id]);
if ($c->fetchColumn() > 0) {
    flash('Cannot delete: this book has transaction history.', 'error');
} else {
    $pdo->prepare('DELETE FROM books WHERE id=?')->execute([$id]);
    flash('Book deleted.');
}
redirect('/books/');
