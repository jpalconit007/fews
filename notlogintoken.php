
<?php 
    
    if (isset($_SESSION['user_id'])!=null) {
        header("refresh:0");
        // Redirect to the login page if user is not logged in
        header("Location: admin_dashboard.php");       
    } 
?>
