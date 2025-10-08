<?php
require_once 'includes/db.php';
session_start();

// Handle both add and edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match('/^[\d\s\-+]+$/', $phone)) {
        $errors[] = "Invalid phone number format";
    }

    if (empty($errors)) {
        try {
            if ($action === 'edit' && $id) {
                // Update existing resident
                $stmt = $conn->prepare("UPDATE residents SET name=?, address=?, phone=? WHERE resident_id=?");
                $stmt->bind_param("sssi", $name, $address, $phone, $id);
                $successMessage = "Resident updated successfully!";
            } else {
                // Add new resident
                $stmt = $conn->prepare("INSERT INTO residents (name, address, phone) VALUES (?, ?, ?)");
                
                $stmt->bind_param("sss", $name, $address, $phone);
                $successMessage = "Resident added successfully!";
            }

            if ($stmt->execute()) {
                $_SESSION['message'] = $successMessage;
                $_SESSION['message_type'] = "success";
                header("Location: admin_residents.php");
                exit();
            } else {
                throw new Exception("Database error: " . $conn->error);
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    // Handle errors
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = compact('name', 'address', 'phone');
    header("Location: admin_residents.php?error=1");
    exit();
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM residents WHERE resident_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Resident deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting resident: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: admin_residents.php");
    exit();
}

header("Location: admin_residents.php");
exit();
?>