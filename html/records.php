<?php include("logintoken.php"); ?>
<?php require_once 'includes/db.php'; ?>
<?php
  //session_start();
  
  // Fetch sensor records from database using mysqli
  $records = [];
  $error = '';
  
  // Check if database connection exists
  if (isset($conn) && $conn instanceof mysqli) {
    // Check if connection is valid
    if ($conn->connect_error) {
      $error = "Database connection failed: " . $conn->connect_error;
    } else {
      // Using prepared statement for security
      $query = "SELECT reading_date, reading_time, water_level FROM sensor_readings2 ORDER BY reading_date DESC, reading_time DESC";
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
  } else {
    $error = "Database connection not established properly.";
  }
  
  // Function to determine risk level based on water level in meters
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
        /* Your existing CSS styles here */
        body{
            background-color: #f0f8ff;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            padding: 20px;
            margin-left: 250px;
        }

        .admin-features {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .table-responsive {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow-x: auto;
            min-height: 400px;
            margin-bottom: 20px;
        }
        
        .table th {
            background-color: #1a6fc4;
            color: white;
            position: sticky;
            top: 0;
            font-weight: 600;
            padding: 12px 15px;
        }
        
        .table td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(26, 111, 196, 0.05);
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: #1a6fc4;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0c4d8c;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-success {
            background-color: #2ecc71;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .print-only {
            display: none;
        }
        
        .no-records {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }

        .admin-header {
            background: linear-gradient(135deg, #1a6fc4 0%, #0c4d8c 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .admin-header h1 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .error-alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="no-print">
            <div class="admin-header">
                <h1><i class="fas fa-table"></i> Latest Sensor Records (Water Level in Meters)</h1>
            </div>
        </div>
                
        <div class="admin-features">
            <?php if (!empty($error)): ?>
                <div class="error-alert">
                    <strong>Error:</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="print-only">Flood Monitoring Record - Generated on <span id="print-date"></span></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>River Level (m)</th>
                            <th>Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (is_array($records) && count($records) > 0): ?>
                            <?php foreach ($records as $record): ?>
                                <?php 
                                    $riskData = getRiskLevel($record['water_level']);
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['reading_date']); ?></td>
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
                <button class="btn btn-primary" onclick="printRecords()">
                    <i class="fas fa-print"></i> Print Records
                </button>
                <button class="btn btn-success" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i> Export as PDF
                </button>
            </div>
        </div>
    </div>

    <script>
        function printRecords() {
            window.print();
        }
        
        // Set print date
        document.getElementById('print-date').textContent = new Date().toLocaleDateString();

        function exportToPDF(){
            window.location.href = 'generatepdf.php';
        }
    </script>
        
</body>
</html>