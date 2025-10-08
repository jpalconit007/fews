<?php
require 'includes/db_config.php';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $stmt = $pdo->query("SELECT water_level FROM sensor_readings ORDER BY reading_time DESC LIMIT 1");
    
    if ($level = $stmt->fetchColumn()) {
        echo "✅ Connection successful. Latest reading: " . $level . "ft";
    } else {
        echo "✅ Connection successful but no data found";
    }
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>