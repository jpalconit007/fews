<?php include("logintoken.php"); ?>
<?php require_once 'includes/db.php'; ?>
<?php
  //session_start();
$river_alerts = [];
  $db_error = "";
  
  try {
      // Check database connection
      if (!$conn) {
          throw new Exception("Database connection failed");
      }
      
      $sql = "
          SELECT 
            s.*,
            -- Determine status based on water level thresholds
            CASE 
              WHEN s.water_level >= 5.0 THEN 'danger'
              WHEN s.water_level >= 3.5 THEN 'warning'
              WHEN s.water_level >= 2.5 THEN 'alert'
              ELSE 'normal'
            END as status_level
          FROM sensor_readings2 s 
          WHERE s.reading_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
          ORDER BY s.reading_time DESC
          LIMIT 5
      ";
      
      $result = $conn->query($sql);
      
      if (!$result) {
          throw new Exception("Failed to execute SQL query: " . $conn->error);
      }
      
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              $river_alerts[] = $row;
          }
      }
      
  } catch (Exception $e) {
      // Store error message for display
      $db_error = "Database Error: " . $e->getMessage();
      error_log("River status notification error: " . $e->getMessage());
  }
  
  // Function to determine alert class based on status level
  function getRiverAlertClass($status) {
      switch ($status) {
          case 'danger':
              return 'fas fa-exclamation-circle text-danger';
          case 'warning':
              return 'fas fa-exclamation-triangle text-warning';
          case 'alert':
              return 'fas fa-info-circle text-info';
          case 'normal':
          default:
              return 'fas fa-check-circle text-success';
      }
  }
  
  // Function to get status text for display
  function getRiverStatusText($status) {
      switch ($status) {
          case 'danger':
              return 'Danger - Flood Risk';
          case 'warning':
              return 'Warning - High Water Level';
          case 'alert':
              return 'Alert - Elevated Levels';
          case 'normal':
          default:
              return 'Normal Conditions';
      }
  }
  
  // Function to format relative time
  function relativeTime($timestamp) {
      if (empty($timestamp)) return 'Unknown time';
      
      $time = strtotime($timestamp);
      $now = time();
      $diff = $now - $time;
      
      if ($diff < 60) {
          return 'just now';
      } else if ($diff < 3600) {
          $mins = floor($diff / 60);
          return $mins . ' minute' . ($mins != 1 ? 's' : '') . ' ago';
      } else if ($diff < 86400) {
          $hours = floor($diff / 3600);
          return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ago';
      } else {
          $days = floor($diff / 86400);
          return $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<style>
    body{
        background-color: #f0f8ff;
            color: #333;
    }
    .notification-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 10px;
            margin-bottom: 30px;
            margin-left: 15px;
            
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .notification-header h2 {
            color: #243c74;
            font-size: 22px;
        }

        .mark-all-read {
        background: linear-gradient(135deg, #243c74, #1a68d1);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .mark-all-read:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .notification-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .notification-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: flex-start;
        transition: all 0.3s ease;
    }
    .notification-item:hover {
        background-color: #f9f9f9;
    }
    .notification-item.unread {
        background-color: #f0f7ff;
    }
    .notification-icon {
        margin-right: 15px;
        font-size: 18px;
        min-width: 25px;
        text-align: center;
        padding-top: 3px;
    }
    .notification-content {
        flex-grow: 1;
    }
    .notification-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
    }
    .notification-message {
        color: #666;
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 8px;
    }
    .notification-time {
        color: #999;
        font-size: 12px;
        display: flex;
        align-items: center;
    }
    .notification-time i {
        margin-right: 5px;
        font-size: 12px;
    }
    .notification-actions {
        display: flex;
        gap: 10px;
    }
    .notification-action {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        font-size: 12px;
        transition: color 0.2s;
    }
    .notification-action:hover {
        color: #1a68d1;
    }
    /* River status specific styles */
    .river-alert.danger {
        border-left: 4px solid #dc3545;
    }
    .river-alert.warning {
        border-left: 4px solid #ffc107;
    }
    .river-alert.alert {
        border-left: 4px solid #17a2b8;
    }
    .river-alert.normal {
        border-left: 4px solid #28a745;
    }
    .river-status-badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        margin-left: 10px;
    }
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    .sensor-data {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-top: 8px;
    }
    .sensor-data span {
        font-size: 13px;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        display: flex;
        align-items: center;
    }
    .sensor-data i {
        margin-right: 5px;
    }

    @media (max-width: 768px) {
        .sensor-data {
            grid-template-columns: 1fr;
        }
    }
</style>
<body>
    <!-- Include the sidebar -->
    <?php include 'includes/admin_sidebar.php'; ?>
    

    <div class="main-content">
        <div class="admin-header">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
        </div>

        <?php if (!empty($db_error)): ?>
            <div class="db-error">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Database Error:</strong> <?php echo htmlspecialchars($db_error); ?>
            </div>
        <?php endif; ?>

        <div class="notification-container">
            <div class="notification-header">
                <h2>River Status Alerts</h2>
                <button class="mark-all-read" onclick="markAllAsRead()">
                    <i class="fas fa-check-double"></i> Mark all as read
                </button>
            </div>

            <ul class="notification-list">
                <?php if (!empty($river_alerts)): ?>
                    <?php foreach ($river_alerts as $alert): ?>
                        <li class="notification-item unread river-alert <?php echo $alert['status_level']; ?>">
                            <div class="notification-icon">
                                <i class="<?php echo getRiverAlertClass($alert['status_level']); ?>"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    River Status: <?php echo getRiverStatusText($alert['status_level']); ?>
                                    <span class="river-status-badge badge-<?php echo $alert['status_level']; ?>">
                                        <?php echo strtoupper($alert['status_level']); ?>
                                    </span>
                                </div>
                                <div class="notification-message">
                                    <!-- <strong>Sensor ID:</strong> <?php echo htmlspecialchars($alert['sensor_id'] ?? 'N/A'); ?><br> -->
                                    <strong>Location:</strong> <?php echo htmlspecialchars($alert['location'] ?? 'Unknown'); ?>
                                    
                                    <div class="sensor-data">
                                        <?php if (isset($alert['water_level'])): ?>
                                            <span><i class="fas fa-water"></i> Level: <?php echo $alert['water_level']; ?>m</span>
                                        <?php endif; ?>
                                        
                                        <!-- <?php if (isset($alert['flow_rate'])): ?>
                                            <span><i class="fas fa-wind"></i> Flow: <?php echo $alert['flow_rate']; ?> m/s</span>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($alert['temperature'])): ?>
                                            <span><i class="fas fa-thermometer-half"></i> Temp: <?php echo $alert['temperature']; ?>°C</span>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($alert['ph_level'])): ?>
                                            <span><i class="fas fa-vial"></i> pH: <?php echo $alert['ph_level']; ?></span>
                                        <?php endif; ?> -->
                                    </div>
                                </div>
                                <div class="notification-time">
                                    <i class="far fa-clock"></i> 
                                    <?php 
                                        echo relativeTime($alert['reading_time'] ?? '');
                                    ?>
                                </div>
                            </div>
                            <div class="notification-actions">
                                <button class="notification-action" onclick="markAsRead(this)">
                                    <i class="fas fa-check"></i> Mark as read
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="notification-item">
                        <div class="notification-icon">
                            <i class="fas fa-water"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">No River Status Data</div>
                            <div class="notification-message">
                                <?php if (empty($db_error)): ?>
                                    There are currently no river sensor readings to display.
                                <?php else: ?>
                                    Cannot load river data due to database error.
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script>
        function markAsRead(button) {
            const notificationItem = button.closest('.notification-item');
            notificationItem.classList.remove('unread');
            
            // You would typically send an AJAX request here to update the server
            showToast('Notification marked as read');
        }

        function markAllAsRead() {
            const unreadNotifications = document.querySelectorAll('.notification-item.unread');
            unreadNotifications.forEach(notification => {
                notification.classList.remove('unread');
            });
            
            showToast('All notifications marked as read');
        }

        function showToast(message) {
            // Create toast element
            const toast = document.createElement('div');
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.backgroundColor = '#243c74';
            toast.style.color = 'white';
            toast.style.padding = '12px 20px';
            toast.style.borderRadius = '5px';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            toast.style.zIndex = '1000';
            toast.style.transition = 'all 0.3s ease';
            toast.style.transform = 'translateY(100px)';
            toast.style.opacity = '0';
            toast.innerText = message;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            }, 10);
            
            // Animate out after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateY(100px)';
                toast.style.opacity = '0';
                
                // Remove after animation completes
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
        
        // Auto-refresh river status every 5 minutes
        setInterval(() => {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>