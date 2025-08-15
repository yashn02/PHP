<?php
session_start();


function flashMessages() {
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n";
        unset($_SESSION['success']);
    }
}


function validateProfile() {
    foreach (['first_name', 'last_name', 'email', 'headline', 'summary'] as $field) {
        if (empty($_POST[$field])) {
            return "All fields are required";
        }
    }
    if (strpos($_POST['email'], '@') === false) {
        return "Email address must contain @";
    }
    return true;
}


function validatePos() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i])) continue;
        if (!isset($_POST['desc'.$i])) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        if (strlen($year) == 0 || strlen($desc) == 0) {
            return "All fields are required";
        }
        if (!is_numeric($year)) {
            return "Year must be numeric";
        }
    }
    return true;
}
?>