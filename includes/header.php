<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
$flash = flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Library Management System</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="<?= BASE_URL ?>/index.php">📚 Library</a>
  <a href="<?= BASE_URL ?>/books/">Books</a>
  <a href="<?= BASE_URL ?>/members/">Members</a>
  <a href="<?= BASE_URL ?>/transactions/">Transactions</a>
  <a href="<?= BASE_URL ?>/transactions/issue.php" class="btn-nav">Issue Book</a>
</nav>
<main class="container">
<?php if ($flash): ?>
  <div class="alert <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>
