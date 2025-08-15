<?php
session_start();
require_once "pdo.php";

// ✅ Access control
if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

if (!isset($_GET['profile_id'])) {
    die("Missing profile_id");
}

// ✅ Fetch profile and verify ownership
$stmt = $pdo->prepare("SELECT first_name, last_name FROM Profile WHERE profile_id = :pid AND user_id = :uid");
$stmt->execute([
    ':pid' => $_GET['profile_id'],
    ':uid' => $_SESSION['user_id']
]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Access denied or profile not found");
}

// ✅ Handle POST request to delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :pid AND user_id = :uid");
    $stmt->execute([
        ':pid' => $_GET['profile_id'],
        ':uid' => $_SESSION['user_id']
    ]);
    $_SESSION['success'] = "Profile deleted";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Delete Profile</title>
</head>
<body>
<h1>Confirm Deletion</h1>
<p>Are you sure you want to delete the profile of <strong><?= htmlentities($row['first_name'] . ' ' . $row['last_name']) ?></strong>?</p>
<form method="post">
  <input type="submit" value="Delete">
  <a href="index.php">Cancel</a>
</form>
</body>
</html>