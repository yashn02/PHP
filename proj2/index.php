<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yash Naktode</title>
</head>
<body>

<h1>Welcome to the Autos Database</h1>
<p><a href="login.php">Please log in</a></p>
    <?php
    
    $_SESSION['name'] = $_POST['email'];
header("Location: view.php");
return;

?>
</body>
</html>