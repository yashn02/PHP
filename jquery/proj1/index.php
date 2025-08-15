<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yash Naktode</title>
</head>
<body>
    <?php
session_start();
require_once "pdo.php";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Resume Database - <?php echo htmlentities($_SESSION['name'] ?? 'Guest'); ?></title>
</head>
<body>
<h1>Resume Profiles</h1>

<?php
if (isset($_SESSION['success'])) {
    echo '<p style="color:green">'.htmlentities($_SESSION['success'])."</p>";
    unset($_SESSION['success']);
}
$stmt = $pdo->query("SELECT profile_id, first_name, last_name FROM Profile");
echo "<table border='1'>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>";
    echo "<a href='view.php?profile_id=" . $row['profile_id'] . "'>";
    echo htmlentities($row['first_name'] . ' ' . $row['last_name']);
    echo "</a></td>";
    if (isset($_SESSION['user_id'])) {
        echo "<td><a href='edit.php?profile_id=" . $row['profile_id'] . "'>Edit</a></td>";
        echo "<td><a href='delete.php?profile_id=" . $row['profile_id'] . "'>Delete</a></td>";
    }
    echo "</tr>";
}
echo "</table>";

if (isset($_SESSION['user_id'])) {
    echo '<p><a href="add.php">Add New Profile</a></p>';
    echo '<p><a href="logout.php">Logout</a></p>';
} else {
    echo '<p><a href="login.php">Login</a></p>';
}
?>
</body>
</html>
</body>
</html>