<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yash Naktode</title>
</head>
<body>
    <?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=forjq', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
</body>
</html>