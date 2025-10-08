<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Log</title>
    <style>
        body{
            background-color: #f0f8ff;
            color: #333;
        }

        /* .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2px;
            
        } */

        /* Table styling */
        .admin-features {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            min-height: 550px;
        }
        
        .table-responsive {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow-x: auto;
            min-height: 500px;
            
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
        
    </style>
</head>
<body>
    <?php include 'includes/admin_sidebar.php'; ?>
    <?php
    // Fetch activity logs from papertrail table
    $logs = [];
    $error = '';
    
    // Check if database connection exists
    if (isset($conn) && $conn instanceof mysqli) {
        // Check if connection is valid
        if ($conn->connect_error) {
            $error = "Database connection failed: " . $conn->connect_error;
        } else {
            // Query to get logs - adjust table/column names as needed
            $query = "SELECT * FROM papertrail ORDER BY dateadded DESC";
            $result = $conn->query($query);
            
            if ($result) {
                // Check if we have any rows
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $logs[] = $row;
                    }
                }
                $result->free();
            } else {
                $error = "Database query error: " . $conn->error;
            }
        }
    } else {
        $error = "Database connection not established properly.";
    }
    ?>
    
    <div class="main-content">
        <div class="admin-header">
            <h1><i class="fa-solid fa-book-open-reader"></i> Activity Logs</h1>
        </div>

        <div class="admin-features">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> <?php echo $error; ?>
                    <div class="mt-2">
                        <small>Please check if the 'papertrail' table exists in your database.</small>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Module</th>
                            <th>Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td>
                                        <?php 
                                        // Display user information - adjust based on your table structure
                                        if (isset($log['firstname']) && isset($log['lastname'])) {
                                            echo htmlspecialchars($log['firstname'] . ' ' . $log['lastname']);
                                        } elseif (isset($log['fullname'])) {
                                            echo htmlspecialchars($log['fullname']);
                                        } else {
                                            echo 'Unknown User';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (isset($log['dateadded'])) {
                                            echo date('M j, Y', strtotime($log['dateadded']));
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (isset($log['dateadded'])) {
                                            echo date('g:i A', strtotime($log['dateadded']));
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (isset($log['module'])) {
                                            echo htmlspecialchars($log['module']);
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (isset($log['operation'])) {
                                            echo htmlspecialchars($log['operation']);
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php $counter++; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="no-records">
                                    <?php echo empty($error) ? 'No activity logs found.' : 'Could not load logs due to an error.'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>