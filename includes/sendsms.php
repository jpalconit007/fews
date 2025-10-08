<?php
require '../vendor/autoload.php';
require 'db_config.php';

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

// Configuration with multiple alert levels
$alertConfig = [
    'thresholds' => [
        'warning' => 2.0,      // Initial warning level
        'severe' => 2.5,       // More dangerous level
        'critical' => 3.0      // Emergency level
    ],
    'brand' => 'DONA IMELDA',
    'recipient' => '639383871675',
    'cooldown' => [
        'warning' => 10,      // 5 minutes for warning alerts
        'severe' => 10,       // 3 minutes for severe alerts
        'critical' => 10       // 1 minute for critical alerts
    ]
];

try {
    // 1. Establish database connection
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(3, 2);

    // 2. Fetch latest water level
    $rawValue = $pdo->query("SELECT water_level FROM sensor_readings ORDER BY reading_time DESC LIMIT 1")->fetchColumn();

    if ($rawValue === false) {
        throw new Exception("No water level data found");
    }

    // 3. Extract numeric value
    if (!preg_match('/(\d+\.?\d*)/', $rawValue, $matches)) {
        throw new Exception("Invalid water level format: " . $rawValue);
    }
    $currentLevel = (float)$matches[1];
    $currentTime = time();

    // 4. Determine alert level
    $alertType = null;
    $message = '';
    
    if ($currentLevel >= $alertConfig['thresholds']['critical']) {
        $alertType = 'critical';
        $message = "🚨🚨 CRITICAL FLOOD EMERGENCY! Water level: " . number_format($currentLevel, 2) . "ft - EVACUATE IMMEDIATELY!";
    } elseif ($currentLevel >= $alertConfig['thresholds']['severe']) {
        $alertType = 'severe';
        $message = "🚨 SEVERE FLOOD ALERT! Water level: " . number_format($currentLevel, 2) . "ft - PREPARE TO EVACUATE!";
    } elseif ($currentLevel >= $alertConfig['thresholds']['warning']) {
        $alertType = 'warning';
        $message = "⚠️ FLOOD WARNING! Water level: " . number_format($currentLevel, 2) . "ft - MONITOR CLOSELY";
    }

    // 5. Process alert if needed
    if ($alertType) {
        $lastAlertFile = "last_alert_$alertType.txt";
        $lastAlert = @file_get_contents($lastAlertFile);
        
        if (!$lastAlert || ($currentTime - $lastAlert) >= $alertConfig['cooldown'][$alertType]) {
            // Send SMS alert
            $client = new Client(new Basic($apiKey, $apiSecret));
            $response = $client->sms()->send(
                new SMS(
                    $alertConfig['recipient'],
                    $alertConfig['brand'],
                    $message
                )
            );

            // Process response
            $sms = $response->current();
            if ($sms->getStatus() == 0) {
                file_put_contents($lastAlertFile, $currentTime);
                $logMsg = sprintf(
                    "[%s] %s ALERT: %.2fft | ID: %s\n",
                    date('Y-m-d H:i:s'),
                    strtoupper($alertType),
                    $currentLevel,
                    $sms->getMessageId()
                );
                file_put_contents('alerts.log', $logMsg, FILE_APPEND);
                echo $logMsg;
            } else {
                throw new Exception("SMS failed: " . $sms->getErrorText());
            }
        } else {
            echo date('Y-m-d H:i:s') . " | $alertType alert on cooldown (next alert at " . 
                 date('H:i:s', $lastAlert + $alertConfig['cooldown'][$alertType]) . ")\n";
        }
    } else {
        echo date('Y-m-d H:i:s') . " | Level: " . number_format($currentLevel, 2) . "ft (Safe)\n";
    }

} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo "Database error - check configuration\n";
} catch (Exception $e) {
    error_log("Alert Error: " . $e->getMessage());
    echo "Alert processing failed: " . $e->getMessage() . "\n";
}
?>