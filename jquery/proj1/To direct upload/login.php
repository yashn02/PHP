
    <?php
session_start();
require_once "pdo.php";

$salt = 'XyZzy12*_';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['pass'])) {
        $_SESSION['error'] = "Both fields must be filled out";
        header("Location: login.php");
        return;
    }

    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email must contain @";
        header("Location: login.php");
        return;
    }

    $check = hash('md5', $salt . $_POST['pass']);
    $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email = :em AND password = :pw");
    $stmt->execute([
        ':em' => $_POST['email'],
        ':pw' => $check
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: index.php");
        return;
    } else {
        $_SESSION['error'] = "Incorrect email or password";
        header("Location: login.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Yash Naktode</title>
  <script>
    function doValidate() {
      console.log('Validating...');
      const email = document.getElementById('id_email').value;
      const pw = document.getElementById('id_1723').value;
      if (!email || !pw) {
        alert("Both fields must be filled out");
        return false;
      }
      if (!email.includes('@')) {
        alert("Email must contain @");
        return false;
      }
      return true;
    }
  </script>
</head>
<body>
<h1>Please Log In</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>";
    unset($_SESSION['error']);
}
?>
<form method="post">
  <p>Email: <input type="text" name="email" id="id_email"></p>
  <p>Password: <input type="password" name="pass" id="id_1723"></p>
  <p><input type="submit" onclick="return doValidate();" value="Log In">
     <a href="index.php">Cancel</a></p>
</form>
</body>
</html>