<?php
session_start();
require_once "pdo.php";

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel'])) {
        header("Location: view.php");
        return;
    }

    // Validate input
    if (empty($_POST['make']) || empty($_POST['year']) || empty($_POST['mileage'])) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    }

    if (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
        return;
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)");
    $stmt->execute([
        ':mk' => $_POST['make'],
        ':yr' => $_POST['year'],
        ':mi' => $_POST['mileage']
    ]);

    $_SESSION['success'] = "Record inserted";
    header("Location: view.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Automobile</title>
</head>
<body>
    <h1>Tracking Autos for <?= htmlentities($_SESSION['name']) ?></h1>

    <?php
    // Display error message using flash pattern
    if (isset($_SESSION['error'])) {
        echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
        unset($_SESSION['error']);
    }
    ?>

    <form method="POST">
        <p>Make: <input type="text" name="make"></p>
        <p>Year: <input type="text" name="year"></p>
        <p>Mileage: <input type="text" name="mileage"></p>
        <p>
            <input type="submit" value="Add">
            <input type="submit" name="cancel" value="Cancel">
        </p>
    </form>
</body>
</html>