<?php
require_once 'includes/db.php';

// Check if the ID parameter is present
if (isset($_GET['DELid']) && is_numeric($_GET['DELid'])) {
    $id = (int)$_GET['DELid'];
    $date = date('Y-m-d H:i:s');
    
    // Start session for messages
    session_start();
    
    try {
        // Instead of deleting, update the status to 'inactive'
        $stmt = $conn->prepare("UPDATE residents SET status = 'inactive' WHERE resident_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO papertrail (userid, firstname, lastname, dateadded, module, operation) VALUES ('$_SESSION[user_id]','$_SESSION[fname]','$_SESSION[lname]','$date','Residents','Deleted a resident')");
                
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Resident marked as inactive successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "No resident found with that ID.";
                $_SESSION['message_type'] = "warning";
            }
        } else {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect back to residents page
    header("Location: admin_residents.php");
    exit();
}

// If no ID parameter, redirect back
header("Location: admin_residents.php");
exit();
?>