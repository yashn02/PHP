<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
require_once "pdo.php";
if (!isset($_GET['profile_id'])) {
    die("Missing profile_id");
}
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute([':pid' => $_GET['profile_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    die("Profile not found");
}
?>
<!DOCTYPE html>
<html>
<head><title>View Profile</title></head>
<body>
<h1>Profile Details</h1>
<p>Name: <?= htmlentities($row['first_name'] . ' ' . $row['last_name']) ?></p>
<p>Email: <?= htmlentities($row['email']) ?></p>
<p>Headline: <?= htmlentities($row['headline']) ?></p>
<p>Summary: <?= htmlentities($row['summary']) ?></p>
<p><a href="index.php">Back</a></p>
</body>
</html>
</body>
</html>