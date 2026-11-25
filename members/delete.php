<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/members/');
csrf_check();
$id = (int)($_POST['id'] ?? 0);

$c = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE member_id=?');
$c->execute([$id]);
if ($c->fetchColumn() > 0) {
    flash('Cannot delete: this member has transaction history. Mark them inactive instead.', 'error');
} else {
    $pdo->prepare('DELETE FROM members WHERE id=?')->execute([$id]);
    flash('Member deleted.');
}
redirect('/members/');
