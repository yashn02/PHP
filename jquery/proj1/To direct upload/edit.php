<?php
session_start();
require_once "pdo.php";
if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}
if (!isset($_GET['profile_id'])) {
    die("Missing profile_id");
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid");
$stmt->execute([':pid' => $_GET['profile_id'], ':uid' => $_SESSION['user_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) ||
        empty($_POST['headline']) || empty($_POST['summary'])) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em,
        headline = :he, summary = :su WHERE profile_id = :pid AND user_id = :uid");
    $stmt->execute([
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_GET['profile_id'],
        ':uid' => $_SESSION['user_id']
    ]);
    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Profile</title></head>
<body>
<h1>Edit Profile</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>";
    unset($_SESSION['error']);
}
?>
<form method="post">
  <p>First Name: <input type="text" name="first_name" value="<?= htmlentities($row['first_name']) ?>"></p>
  <p>Last Name: <input type="text" name="last_name" value="<?= htmlentities($row['last_name']) ?>"></p>
  <p>Email: <input type="text" name="email" value="<?= htmlentities($row['email']) ?>"></p>
  <p>Headline: <input type="text" name="headline" value="<?= htmlentities($row['headline']) ?>"></p>
  <p>Summary:<br><textarea name="summary" rows="6" cols="40"><?= htmlentities($row['summary']) ?></textarea></p>
  <p><input type="submit" value="Save">
     <a href="index.php">Cancel</a></p>
</form>
</body>
</html>