<?php
session_start();
require_once "util.php";


$pdo = new PDO('mysql:host=localhost;port=3306;dbname=forjq', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid");
$stmt->execute([
    ':pid' => $_GET['profile_id'],
    ':uid' => $_SESSION['user_id']
]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile) {
    $_SESSION['error'] = "Profile not found or unauthorized";
    header("Location: index.php");
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = validateProfile();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE Profile SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su WHERE profile_id=:pid");
    $stmt->execute([
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_GET['profile_id']
    ]);

    $stmt = $pdo->prepare("DELETE FROM Position WHERE profile_id=:pid");
    $stmt->execute([':pid' => $_GET['profile_id']]);

    $msg = validatePos();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }

    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i]) || !isset($_POST['desc' . $i])) continue;
        $stmt = $pdo->prepare("INSERT INTO Position (profile_id, rank, year, description)
            VALUES (:pid, :rank, :year, :desc)");
        $stmt->execute([
            ':pid' => $_GET['profile_id'],
            ':rank' => $rank++,
            ':year' => $_POST['year' . $i],
            ':desc' => $_POST['desc' . $i]
        ]);
    }

    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank");
$stmt->execute([':pid' => $_GET['profile_id']]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Profile</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
</head>
<body>
<div class="container">
<h1>Edit Profile</h1>
<?php flashMessages(); ?>
<form method="post">
  <p>First Name: <input type="text" name="first_name" value="<?= htmlentities($profile['first_name']) ?>"></p>
  <p>Last Name: <input type="text" name="last_name" value="<?= htmlentities($profile['last_name']) ?>"></p>
  <p>Email: <input type="text" name="email" value="<?= htmlentities($profile['email']) ?>"></p>
  <p>Headline: <input type="text" name="headline" value="<?= htmlentities($profile['headline']) ?>"></p>
  <p>Summary:<br><textarea name="summary" rows="8" cols="80"><?= htmlentities($profile['summary']) ?></textarea></p>
  <p>Position: <input type="button" id="addPos" value="+"></p>
  <div id="position_fields">
    <?php
    $countPos = 0;
    foreach ($positions as $pos) {
        $countPos++;
        echo '<div id="position' . $countPos . '">';
        echo '<p>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($pos['year']) . '" />';
        echo '<input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove(); return false;"></p>';
        echo '<textarea name="desc' . $countPos . '" rows="8" cols="80">' . htmlentities($pos['description']) . '</textarea>';
        echo '</div>';
    }
    ?>
  </div>
  <p><input type="submit" value="Save"></p>
</form>
<script>
let countPos = <?= $countPos ?>;
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