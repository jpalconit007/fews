
<?php 
    session_start();
    if (isset($_SESSION['user_id'])==null) {
        // Redirect to the login page if user is not logged in
        header("Location: login.php");
        die();        
    } 
?>
