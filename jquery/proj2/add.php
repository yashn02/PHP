<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=forjq', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
require_once "util.php";

if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = validateProfile();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
        VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']
    ]);
    $profile_id = $pdo->lastInsertId();

    $msg = validatePos();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) continue;
        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
            VALUES (:pid, :rank, :year, :desc)');
        $stmt->execute([
            ':pid' => $profile_id,
            ':rank' => $rank++,
            ':year' => $_POST['year'.$i],
            ':desc' => $_POST['desc'.$i]
        ]);
    }

    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Profile</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
</head>
<body>
<div class="container">
<h1>Add Profile</h1>
<?php flashMessages(); ?>
<form method="post">
  <p>First Name: <input type="text" name="first_name"></p>
  <p>Last Name: <input type="text" name="last_name"></p>
  <p>Email: <input type="text" name="email"></p>
  <p>Headline: <input type="text" name="headline"></p>
  <p>Summary:<br><textarea name="summary" rows="8" cols="80"></textarea></p>
  <p>Position: <input type="button" id="addPos" value="+"></p>
  <div id="position_fields"></div>
  <p><input type="submit" value="Add"></p>
</form>
<script>
let countPos = 0;
$(document).ready(function(){
  $('#addPos').click(function(event){
    event.preventDefault();
    if (countPos >= 9) {
      alert("Maximum of nine position entries exceeded");
      return;
    }
    countPos++;
    $('#position_fields').append(
      `<div id="position${countPos}">
        <p>Year: <input type="text" name="year${countPos}" />
        <input type="button" value="-" onclick="$('#position${countPos}').remove(); return false;"></p>
        <textarea name="desc${countPos}" rows="8" cols="80"></textarea>
      </div>`
    );
  });
});
</script>
</div>
</body>
</html>