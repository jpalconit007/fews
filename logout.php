<?php
session_start();
require_once("includes/db.php");
//Log action
$date = date('Y-m-d H:i:s');
$stmt = $conn->prepare("INSERT INTO papertrail (userid, firstname, lastname, dateadded, module, operation) VALUES ('$_SESSION[user_id]','$_SESSION[fname]','$_SESSION[lname]','$date','Dashboard','logged out')");
$stmt->execute();
// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>