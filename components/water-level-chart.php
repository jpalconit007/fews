<?php
function renderWaterLevelChart() {
    date_default_timezone_set('Asia/Manila');
    
    $db = new PDO('mysql:host=localhost;dbname=fews', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET time_zone = '+08:00'");

    try {
        // Fetch data from the last 6 hours
        $query = $db->query("
            SELECT 
                water_level,
                DATE_FORMAT(reading_time, '%h:%i%p') as time_label,
                UNIX_TIMESTAMP(reading_time) as timestamp
            FROM sensor_readings 
            WHERE reading_time >= DATE_SUB(NOW(), INTERVAL 6 HOUR)
            ORDER BY reading_time ASC
        ");
        $allReadings = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Process data to get 1-minute intervals and extract numeric values
        $filteredData = [];
        $lastMinute = null;
        
        foreach ($allReadings as $reading) {
            $currentMinute = date('i', $reading['timestamp']);
            if ($currentMinute != $lastMinute) {
                // Extract numeric value from water_level (e.g., "1.5FT" → 1.5)
                preg_match('/(\d+\.?\d*)/', $reading['water_level'], $matches);
                $numericValue = isset($matches[1]) ? (float)$matches[1] : 0;
                
                $filteredData[] = [
                    'water_level' => $numericValue,
                    'time_label' => $reading['time_label']
                ];
                $lastMinute = $currentMinute;
            }
        }
        
        // Limit to last 60 data points
        $filteredData = array_slice($filteredData, -60);
        
        // Prepare data for Chart.js
        $waterLevels = array_column($filteredData, 'water_level');
        $timeLabels = array_column($filteredData, 'time_label');
        
        // Debug output
        echo "<!-- DEBUG DATA:\n";
        echo "First 5 Water Levels: " . implode(", ", array_slice($waterLevels, 0, 5)) . "\n";
        echo "First 5 Time Labels: " . implode(", ", array_slice($timeLabels, 0, 5)) . "\n";
        echo "-->";
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        // Fallback data if database fails
        $waterLevels = [0, 1.2, 1.5, 2.1, 2.4, 3.0];
        $timeLabels = ['8:00am', '8:30am', '9:00am', '9:30am', '10:00am', '10:30am'];
    }
?>
    <div class="col-12">
        <div class="dashboard-card2" style="margin-top: 0; width: 450px; text-align: center;">
            <h5>Water Level Monitoring</h5>
            <div style="height: 300px;">
                <canvas id="waterLevelChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart configuration
        var ctx = document.getElementById('waterLevelChart').getContext('2d');
        var waterLevelChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($timeLabels) ?>,
                datasets: [{
                    label: 'Water Level (meter)',
                    data: <?= json_encode($waterLevels) ?>,
                    borderColor: '#0066cc',
                    backgroundColor: 'rgba(0, 102, 204, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: function(context) {
                        var value = context.dataset.data[context.dataIndex];
                        if (value >= 3) return '#F44336';    // Red for high
                        if (value >= 2) return '#ff8c00';    // Orange for moderate
                        if (value >= 1) return '#FFFF00';    // Yellow for low
                        return '#4CAF50';                   // Green for normal
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        suggestedMax: 5,
                        title: {
                            display: true,
                            text: 'Water Level (meter)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' m';
                            },
                            stepSize: 0.5
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' ft';
                            }
                        }
                    }
                }
            }
        });

        // Auto-refresh every 1 minute
        // setTimeout(function(){
        //     window.location.reload();
        // }, 60000);
    </script>
<?php
}
?>