<?php
require __DIR__ . '/includes/header.php';

$totalTitles = $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
$available   = $pdo->query('SELECT COALESCE(SUM(available_copies),0) FROM books')->fetchColumn();
$members     = $pdo->query("SELECT COUNT(*) FROM members WHERE status='active'")->fetchColumn();
$issued      = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='issued'")->fetchColumn();
$overdue     = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='issued' AND due_date < CURDATE()")->fetchColumn();

$recent = $pdo->query("SELECT t.*, b.title, m.name FROM transactions t
    JOIN books b ON b.id=t.book_id JOIN members m ON m.id=t.member_id
    ORDER BY t.id DESC LIMIT 8")->fetchAll();
?>
<h1>Dashboard</h1>
<div class="stats">
  <div class="stat"><b><?= e($totalTitles) ?></b>Book titles</div>
  <div class="stat"><b><?= e($available) ?></b>Copies available</div>
  <div class="stat"><b><?= e($members) ?></b>Active members</div>
  <div class="stat"><b><?= e($issued) ?></b>Currently issued</div>
  <div class="stat"><b><?= e($overdue) ?></b>Overdue</div>
</div>
<h2>Recent Transactions</h2>
<div class="table-wrap"><table>
<tr><th>#</th><th>Book</th><th>Member</th><th>Issued</th><th>Due</th><th>Status</th></tr>
<?php foreach ($recent as $r): ?>
<tr><td><?= e($r['id']) ?></td><td><?= e($r['title']) ?></td><td><?= e($r['name']) ?></td>
<td><?= e($r['issue_date']) ?></td><td><?= e($r['due_date']) ?></td>
<td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td></tr>
<?php endforeach; if (!$recent): ?><tr><td colspan="6">No transactions yet.</td></tr><?php endif; ?>
</table></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
