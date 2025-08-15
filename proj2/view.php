<?php
session_start();
require_once "pdo.php";

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    die('Not logged in');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Automobile Tracker - View</title>
</head>
<body>
    <h1>Tracking Autos for <?= htmlentities($_SESSION['name']) ?></h1>

    <?php
    // Display success message using flash pattern
    if (isset($_SESSION['success'])) {
        echo '<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n";
        unset($_SESSION['success']);
    }
    ?>

    <h2>Automobiles</h2>
    <ul>
    <?php
    $stmt = $pdo->query("SELECT make, year, mileage FROM autos ORDER BY make");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . htmlentities($row['year']) . " " .
             htmlentities($row['make']) . " / " .
             htmlentities($row['mileage']) . "</li>\n";
    }
    ?>
    </ul>

    <p>
        <a href="add.php">Add New</a> |
        <a href="logout.php">Logout</a>
    </p>
</body>
</html>