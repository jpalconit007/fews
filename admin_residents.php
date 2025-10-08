<?php include("logintoken.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Residents</title>
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
            margin-bottom: 15px;
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
        
        .action-btns .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin-right: 5px;
        }

    </style>
</head>

<body>
    <?php include 'includes/admin_sidebar.php'; ?>

     <div class="main-content">
        <div class="container-fluid">
                <div class="admin-header">
                    <h1><i class="fas fa-users me-2"></i>Residents</h1>
                </div>


                <div class="admin-features">
                    <div class="d-flex justify-content-between mb-2">
                        <h4>Residents List</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResidentModal">
                            <i class="fas fa-plus me-1"></i> Add New Resident
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                            <!-- Add Resident Modal -->
                            <div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addResidentModalLabel"><i class="fas fa-user-plus me-2"></i>Add New Resident</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="process_residents.php" method="POST">

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="residentName" class="form-label">Fullname</label>
                                                    <input type="text" class="form-control" id="residentfname" name="name" required>
                                                </div>
                                               
                                                <div class="mb-3">
                                                    <label for="residentAddress" class="form-label">Address</label>
                                                    <textarea class="form-control" id="residentAddress" name="address" rows="3" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="residentPhone" class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" id="residentPhone" name="phone" required>
                                                </div>
                                            </div>

                                            
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" name="add_resident">Save Resident</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>



                                <!-- Edit Resident Modal (new) -->
                                <div class="modal fade" id="editResidentModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Resident</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="process_residents.php">
                                                <input type="hidden" name="action" value="edit">
                                                <input type="hidden" name="id" id="editResidentId">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Fullname</label>
                                                        <input type="text" class="form-control" id="editfname" name="name" required>
                                                    </div>
                                                
                                                    <div class="mb-3">
                                                        <label class="form-label">Address</label>
                                                        <textarea class="form-control" id="editAddress" name="address" rows="3" required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Phone Number</label>
                                                        <input type="tel" class="form-control" id="editPhone" name="phone" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <?php
                                $sql = "SELECT resident_id, name, address, phone FROM residents WHERE status = 'active'";
                                $result = $conn->query($sql);
                                
                                if ($result && $result->num_rows > 0) {
                                    $count = 1;
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>{$count}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['address']}</td>
                                            <td>{$row['phone']}</td>
                                            <td class='action-btns'>
                                                <button class='btn btn-sm btn-warning edit-resident-btn' 
                                                        data-id='{$row['resident_id']}'
                                                        data-name='{$row['name']}'
                                                        data-address='{$row['address']}'
                                                        data-phone='{$row['phone']}'>
                                                        <i class='fas fa-edit'></i>
                                                </button>
                                        
                                                <a href='process_delete.php?DELid={$row['resident_id']}' class='btn btn-sm btn-danger'>
                                                <i class='fas fa-trash'></i>
                                                </a>
                                            </td>
                                        </tr>";
                                        $count++;
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No residents found</td></tr>";
                                };
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/scripts.js"></script>
    <script>
        Confirm before deleting
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this resident?')) {
                    e.preventDefault();
                }
            });
        });
        
        </script>

        

    
</body>
</html>