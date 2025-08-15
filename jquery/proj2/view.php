<?php

require_once "util.php";

// Inline database connection
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=forjq', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Validate profile_id
if (!isset($_GET['profile_id']) || !is_numeric($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing or invalid profile_id";
    header("Location: index.php");
    return;
}

// Fetch profile
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute([':pid' => $_GET['profile_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $_SESSION['error'] = "Profile not found";
    header("Location: index.php");
    return;
}

// Fetch positions
$stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank");
$stmt->execute([':pid' => $_GET['profile_id']]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>View Profile</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1>Profile Details</h1>
<?php flashMessages(); ?>
<p><strong>Name:</strong> <?= htmlentities($profile['first_name'] . ' ' . $profile['last_name']) ?></p>
<p><strong>Email:</strong> <?= htmlentities($profile['email']) ?></p>
<p><strong>Headline:</strong> <?= htmlentities($profile['headline']) ?></p>
<p><strong>Summary:</strong><br><?= htmlentities($profile['summary']) ?></p>

<?php if (count($positions) > 0): ?>
  <p><strong>Positions:</strong></p>
  <ul>
    <?php foreach ($positions as $pos): ?>
      <li><strong><?= htmlentities($pos['year']) ?>:</strong> <?= htmlentities($pos['description']) ?></li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p><em>No positions listed.</em></p>
<?php endif; ?>

<p><a href="index.php">Back to Index</a></p>
</div>
</body>
</html>