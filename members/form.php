<?php
require __DIR__ . '/../includes/header.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$member = ['name' => '', 'email' => '', 'phone' => '', 'status' => 'active'];
if ($id) {
    $s = $pdo->prepare('SELECT * FROM members WHERE id=?');
    $s->execute([$id]);
    $member = $s->fetch();
    if (!$member) { flash('Member not found.', 'error'); redirect('/members/'); }
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $member = [
        'name'   => trim($_POST['name'] ?? ''),
        'email'  => trim($_POST['email'] ?? ''),
        'phone'  => trim($_POST['phone'] ?? ''),
        'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
    ];
    if ($member['name'] === '') $errors[] = 'Name is required.';
    if (!filter_var($member['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';

    if (!$errors) {
        try {
            if ($id) {
                $pdo->prepare('UPDATE members SET name=?, email=?, phone=?, status=? WHERE id=?')
                    ->execute([$member['name'], $member['email'], $member['phone'], $member['status'], $id]);
                flash('Member updated.');
            } else {
                $pdo->prepare('INSERT INTO members (name,email,phone,status) VALUES (?,?,?,?)')
                    ->execute([$member['name'], $member['email'], $member['phone'], $member['status']]);
                flash('Member added.');
            }
            redirect('/members/');
        } catch (PDOException $ex) {
            $errors[] = $ex->getCode() == 23000 ? 'This email is already registered.' : 'Database error: ' . $ex->getMessage();
        }
    }
}
?>
<h1><?= $id ? 'Edit Member' : 'Add Member' ?></h1>
<?php foreach ($errors as $er): ?><div class="alert error"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <label>Name</label><input name="name" value="<?= e($member['name']) ?>" required>
  <label>Email</label><input type="email" name="email" value="<?= e($member['email']) ?>" required>
  <label>Phone</label><input name="phone" value="<?= e($member['phone']) ?>">
  <label>Status</label>
  <select name="status">
    <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Active</option>
    <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
  </select>
  <p><button class="btn green">Save</button> <a class="btn gray" href="index.php">Cancel</a></p>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
