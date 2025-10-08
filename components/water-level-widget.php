<?php
function renderWaterLevelWidget() {
    // Database connection
    $db = new PDO('mysql:host=localhost;dbname=fews', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        // Fetch the latest water level reading
        $query = $db->query("SELECT water_level, reading_time FROM sensor_readings ORDER BY reading_time DESC LIMIT 1");
        $latestReading = $query->fetch(PDO::FETCH_ASSOC);
        
        // Extract numeric value from string if needed
        $rawValue = $latestReading['water_level'] ?? '0';
        $waterLevel = 0;
        
        if (is_numeric($rawValue)) {
            $waterLevel = (float)$rawValue;
        } else {
            // Handle cases like "1.0ft" - extract the numeric part
            if (preg_match('/(\d+\.?\d*)/', $rawValue, $matches)) {
                $waterLevel = (float)$matches[1];
            }
        }
        
        // Convert to feet for status calculation (assuming stored value is in meters)
        $waterLevelFeet = $waterLevel;

        // Determine status based on water level thresholds (in feet)
        if ($waterLevelFeet == 0) {
            $status = "Normal";
            $statusColor = '#4CAF50';
        } elseif ($waterLevelFeet = 1 && $waterLevelFeet <= 1.5) {
            $status = "Low";
            $statusColor = '#FFFF00';
        } elseif ($waterLevelFeet = 2 && $waterLevelFeet <= 2.5) {
            $status = "Moderate";
            $statusColor = '#ff8c00';
        } elseif ($waterLevelFeet >= 3) {
            $status = "High";
            $statusColor = '#F44336';
        } else {
            // For values between ranges (e.g., 1.6ft - 1.9ft)
            $status = "Undefined";
            $statusColor = '#999999';
        }

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $waterLevel = 0;
        $status = "Data Error";
        $statusColor = '#999999';
        $waterLevelFeet = 0;
    }

    // Status legend data - updated to match your requirements
    $statuses = [
        'Normal' => '#4CAF50',
        'Low' => '#FFFF00',
        'Moderate' => '#ff8c00',
        'High' => '#F44336'
    ];
    ?>
    <div class="col-md-4">
        <div class="dashboard-card1 water-level">
            <div class="card-content">
                <div class="river-info">
                    <p class="river"><strong>San Juan River</strong></p>
                    <p class="water-level">Water Level: </p>
                    <strong class="digits"><?= number_format($waterLevel, 2) ?> m</strong>
                    <!--<small>(<?= number_format($waterLevelFeet, 1) ?> ft)</small>-->
                    
                    <p class="status-text">Status: </p>
                    <span class="status" style="color:<?= $statusColor ?>"><?= $status ?></span>
                </div>
                
                <div class="waterlevel-legend">
                    <p class="legend-title"><strong>Status | Legend</strong></p>
                    <div class="legend-items">
                        <?php foreach ($statuses as $s => $color): ?>
                            <div class="legend-item">
                                <span class="status-label"><?= $s ?></span>
                                <span class="color-box" style="background-color:<?= $color ?>"></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

