<?php include("logintoken.php"); ?>
<?php require_once 'includes/db.php'; ?>
<?php
  //session_start();
  $records = [];
  $error = '';

  if ($conn->connect_error) {
      $error = "Database connection failed: " . $conn->connect_error;
    } else {
      // Using prepared statement for security
      $query = "SELECT reading_time, water_level FROM sensor_readings2 ORDER BY  reading_time DESC LIMIT 10";
      $stmt = $conn->prepare($query);
      
      if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
          // Check if we have any rows
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $records[] = $row;
            }
          }
          $result->free();
        } else {
          $error = "Database query error: " . $conn->error;
        }
        $stmt->close();
      } else {
        $error = "Failed to prepare query: " . $conn->error;
      }
    }

    function getRiskLevel($waterLevel) {
    if ($waterLevel < 0.5) {
      return ['level' => 'Normal', 'class' => 'normal'];
    } elseif ($waterLevel >= 0.5 && $waterLevel < 1.0) {
      return ['level' => 'Warning', 'class' => 'warning'];
    } else {
      return ['level' => 'Danger', 'class' => 'danger'];
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <title>Admin Records</title>
    <style>
        body{
            background-color: #f0f8ff;
            color: #333;
        }


        .admin-features {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            margin-left: 50px;
            margin-right: 50px;
            min-height: 550px;
        }
        
        .table-responsive {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow-x: auto;
            min-height: 450px;
            
        }
        
        .table th {
            background-color: #1a6fc4;
            color: white;
            position: sticky;
            top: 0;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .normal {
            background-color: #2ecc71;
        }
        
        .warning {
            background-color: #f39c12;
        }
        
        .danger {
            background-color: #e74c3c;
        }


        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            position: absolute;
            bottom: 15px;
            right: 15px;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #1a6fc4;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0c4d8c;
        }
        
        .btn-success {
            background-color: #2ecc71;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .print-only {
            display: none;
        }

        @media print {

            body {
                visibility: hidden;
            }
            
            .print-section, .print-section * {
                visibility: visible;
            }
            
            .print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            
            .action-buttons, .btn {
                display: none;
            }
            
            .print-only {
                display: block;
                text-align: center;
                margin-bottom: 20px;
                font-weight: bold;
                color: #1a6fc4;
            }

            .no-print {
                display: none;
            }
            
            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/admin_sidebar.php'; ?>
    <div class="main-content">
            <div class="no-print">
                <div class="admin-header">
                    <h1><i class="fas fa-table"></i> Latest Sensor Records</h1>
                </div>
            </div>
                
        <div class="admin-features">
            <?php if (!empty($error)): ?>
                <div class="error-alert">
                    <strong>Error:</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>River Level (m)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($records) && count($records) > 0): ?>
                            <?php foreach ($records as $record): ?>
                                <?php 
                                    $riskData = getRiskLevel($record['water_level']);
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['reading_time']); ?></td>
                                    <td><?php echo number_format($record['water_level'], 2); ?></td>
                                    <td>
                                        <span class="status-indicator <?php echo $riskData['class']; ?>"></span>
                                        <?php echo $riskData['level']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="no-records">
                                    <?php echo empty($error) ? 'No sensor records found in the database.' : 'Could not load records due to an error.'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                </tbody>
            </table>
            </div>
            
            <div class="action-buttons">
                <button class="btn btn-success" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i> Export as PDF
                </button>
            </div>
        </div>
    </div>
        </div>
    </div>

    <script>

        function exportToPDF(){
            window.location.href = 'generatepdf.php';
        }
    </script>
        
</body>
</html>