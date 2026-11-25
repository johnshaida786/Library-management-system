<?php
require __DIR__ . '/../includes/header.php';
$filter = $_GET['status'] ?? 'all';
$where = match ($filter) {
    'issued'   => "WHERE t.status='issued'",
    'returned' => "WHERE t.status='returned'",
    'overdue'  => "WHERE t.status='issued' AND t.due_date < CURDATE()",
    default    => '',
};
$rows = $pdo->query("SELECT t.*, b.title, m.name,
        IF(t.status='issued' AND t.due_date < CURDATE(), DATEDIFF(CURDATE(), t.due_date), 0) AS days_late
    FROM transactions t JOIN books b ON b.id=t.book_id JOIN members m ON m.id=t.member_id
    $where ORDER BY t.id DESC")->fetchAll();
?>
<h1>Transactions</h1>
<form class="toolbar" method="get">
  <select name="status" onchange="this.form.submit()">
    <?php foreach (['all' => 'All', 'issued' => 'Currently issued', 'overdue' => 'Overdue', 'returned' => 'Returned'] as $k => $v): ?>
      <option value="<?= $k ?>" <?= $filter === $k ? 'selected' : '' ?>><?= $v ?></option>
    <?php endforeach; ?>
  </select>
  <a class="btn green" href="issue.php">+ Issue Book</a>
</form>
<div class="table-wrap"><table>
<tr><th>#</th><th>Book</th><th>Member</th><th>Issued</th><th>Due</th><th>Returned</th><th>Fine (₹)</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $r): $late = $r['days_late'] > 0; ?>
<tr>
  <td><?= e($r['id']) ?></td><td><?= e($r['title']) ?></td><td><?= e($r['name']) ?></td>
  <td><?= e($r['issue_date']) ?></td><td><?= e($r['due_date']) ?></td><td><?= e($r['return_date'] ?? '—') ?></td>
  <td><?= $late ? e($r['days_late'] * FINE_PER_DAY) . ' (accruing)' : e($r['fine']) ?></td>
  <td><span class="badge <?= $late ? 'overdue' : e($r['status']) ?>"><?= $late ? 'overdue' : e($r['status']) ?></span></td>
  <td>
    <?php if ($r['status'] === 'issued'): ?>
    <form class="inline" method="post" action="return.php">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($r['id']) ?>">
      <button class="btn small green">Return</button>
    </form>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="9">No transactions found.</td></tr><?php endif; ?>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
