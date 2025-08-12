<?php
require_once "pdo.php";

if (!isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die("Name parameter missing");
}

$message = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        header("Location: index.php");
        return;
    }

    if (isset($_POST['add'])) {
        if (strlen($_POST['make']) < 1) {
            $message = "Make is required";
        } elseif (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
            $message = "Mileage and year must be numeric";
        } else {
            $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)");
            $stmt->execute([
                ':mk' => $_POST['make'],
                ':yr' => $_POST['year'],
                ':mi' => $_POST['mileage']
            ]);
            $message = "Record inserted";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Autos Database - Yash Naktode</title>
</head>
<body>
<h1>Tracking Autos for <?= htmlentities($_GET['name']) ?></h1>

<?php
if ($message !== false) {
    echo '<p style="color:green;">' . htmlentities($message) . "</p>\n";
}
?>

<form method="POST">
    Make: <input type="text" name="make"><br>
    Year: <input type="text" name="year"><br>
    Mileage: <input type="text" name="mileage"><br>
     <input type='hidden' name='delete_id' value="' . $row['auto_id'] . ">
              <input type='submit' name='delete' value='Delete'>

    <input type="submit" name="add" value="Add">
    <input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>
<ul>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        header("Location: index.php");
        return;
    }

    if (isset($_POST['delete']) && isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM autos WHERE auto_id = :id");
        $stmt->execute([':id' => $_POST['delete_id']]);
        $message = "Record deleted";
    }
}
$stmt = $pdo->query("SELECT make, year, mileage FROM autos ORDER BY make");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<li>" . htmlentities($row['year']) . " " .
         htmlentities($row['make']) . " / " .
         htmlentities($row['mileage']) . "</li>\n";
}
?>
</ul>
</body>

</html>
