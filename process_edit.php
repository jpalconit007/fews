<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'errors' => ['Invalid request method']
    ]);
    exit();
}

// Sanitize inputs
$id = (int)$_POST['resident_id'];
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

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);
    exit();
}

// Update database
try {
    $stmt = $conn->prepare("UPDATE residents SET name = ?, address = ?, phone = ? WHERE resident_id = ?");
    $stmt->bind_param("sssi", $name, $address, $phone, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Resident updated successfully!'
        ]);
    } else {
        throw new Exception("Failed to update resident");
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'errors' => ['Database error: ' . $e->getMessage()]
    ]);
}

?>

