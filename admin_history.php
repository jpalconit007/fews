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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <title>Admin History</title>
    <style>
        body{
            background-color: #f0f8ff;
            color: #333;
        }

        .admin-features {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .table-responsive {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow-x: auto;
            margin-right:40px;
            margin-left:190px;
            
        }
        
        .table th {
            background-color: #1a6fc4;
            color: white;
            position: sticky;
            top: 0;
        }
    </style>
</head>
<body>

<?php include 'includes/admin_sidebar.php'; ?>


<div class="main-content">
        <div class="admin-header">
            <h1><i class="fa-solid fa-chart-simple"></i> History</h1>
        </div>

        <div class="controls mt-2">
            <div class="row gx-3 align-items-center">
                <div class="col-auto d-flex align-items-center">
                    <label for="year" class="form-label mb-0 me-2"><strong>Year:</strong></label>
                    <select id="year" class="form-select" onchange="updateCharts()">
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <label for="category" class="form-label mb-0 me-2"><strong>Category:</strong></label>
                    <select id="category" class="form-select me-2" onchange="updateCharts()">
                        <option value="2023">Past Record of Water Level</option>
                        <option value="2024">Casualties</option>
                    </select>
                    <button type="button" class="manage" data-bs-toggle="modal" data-bs-target="#crudModal">
                        <i class="fas fa-edit"></i> Manage
                    </button>
                </div>
            </div>
        </div>

    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crudModalLabel">Manage Water Data</h5>
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dataYear" class="form-label">Year</label>
                        <select id="dataYear" class="form-select">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                <div class="mb-3">
                    <label for="dataMonth" class="form-label">Month</label>
                    <select id="dataMonth" class="form-select">
                        <option value="Jan">January</option>
                        <option value="Feb">February</option>
                        <option value="Mar">March</option>
                        <option value="Apr">April</option>
                        <option value="May">May</option>
                        <option value="Jun">June</option>
                        <option value="May">July</option>
                        <option value="Jun">August</option>
                        <option value="May">September</option>
                        <option value="Jun">October</option>
                        <option value="May">November</option>
                        <option value="Jun">December</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="waterLevel" class="form-label">Water Level (meter)</label>
                    <input type="number" step="0.01" class="form-control" id="waterLevel">
                </div>
                <div class="mb-3">
                    <label for="casualties" class="form-label">Casualties</label>
                    <input type="number" class="form-control" id="casualties">
                </div>
                <div class="mb-3">
                    <button id="saveData" class="btn btn-primary">Save Data</button>
                    <button id="newData" class="btn btn-success">New Entry</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="contain">
    <div class="chart-container">
            <canvas id="chart1"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="chart2"></canvas>
        </div>
</div>
        
    </div>

    <div class="admin-features">
        <div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Month</th>
                <th id="dataColumn1">Water Level (meter)</th>
                <th id="dataColumn2">Casualties</th>
            </tr>
        </thead>
        <tbody id="historyTableBody">
            
        </tbody>
    </table>
</div>
    </div>

     <script>
    // Initialize charts with empty data
    const ctx1 = document.getElementById('chart1').getContext('2d');
    const ctx2 = document.getElementById('chart2').getContext('2d');

    let chart1 = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Loading data...',
                data: [],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    let chart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Loading data...',
                data: [],
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function updateTable(year, category) {
    fetch(`includes/get_table.php?year=${year}&category=${category}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const tableBody = document.getElementById('historyTableBody');
            tableBody.innerHTML = '';
            
            // Update column headers based on category
            const col1 = document.getElementById('dataColumn1');
            const col2 = document.getElementById('dataColumn2');
            
            if (category === '2023') { // Water Level
                col1.textContent = 'Water Level (meter)';
                col2.textContent = 'Casualties';
            } else { // Casualties
                col1.textContent = 'Casualties';
                col2.textContent = 'Water Level (meter)';
            }
            
            // Populate table rows
            data.forEach(item => {
                const row = document.createElement('tr');
                
                if (category === '2023') {
                    row.innerHTML = `
                        <td>${item.month}</td>
                        <td>${item.water_level}</td>
                        <td>${item.casualties}</td>
                    `;
                } else {
                    row.innerHTML = `
                        <td>${item.month}</td>
                        <td>${item.casualties}</td>
                        <td>${item.water_level}</td>
                    `;
                }
                
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading table data:', error);
            document.getElementById('historyTableBody').innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">Error loading data: ${error.message}</td>
                </tr>
            `;
        });
}


    function updateCharts() {
    const year = document.getElementById('year').value;
    const category = document.getElementById('category').value;

    console.log(`Fetching data for year: ${year}, category: ${category}`);

    // Fetch Bar Chart Data
    fetch(`includes/get_bar_data.php?year=${year}&category=${category}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // console.log('Bar chart data:', data);
            
            chart1.data.labels = data.map(item => item.label);
            chart1.data.datasets[0].data = data.map(item => parseFloat(item.bar_value)); // Use parseFloat for decimals
            chart1.data.datasets[0].label = category === '2023' ? 'Water Level (meter)' : 'Casualties Count';
            chart1.update();
        })
        .catch(error => {
            console.error('Error loading bar chart data:', error);
            chart1.data.datasets[0].label = 'Error: ' + error.message;
            chart1.update();
        });

    // Fetch Line Chart Data
    fetch(`includes/get_line_data.php?year=${year}&category=${category}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // console.log('Line chart data:', data);
            
            chart2.data.labels = data.map(item => item.label);
            chart2.data.datasets[0].data = data.map(item => parseFloat(item.line_value));
            chart2.data.datasets[0].label = category === '2023' ? 'Water Level Trend' : 'Casualties Trend';
            chart2.update();
        })
        .catch(error => {
            console.error('Error loading line chart data:', error);
            chart2.data.datasets[0].label = 'Error: ' + error.message;
            chart2.update();
        });
        updateTable(year, category);
}

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCharts();
    });
</script>


<script>
// CRUD Operations
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('crudModal');
    const saveBtn = document.getElementById('saveData');
    const newBtn = document.getElementById('newData');
    const dataTableBody = document.getElementById('dataTableBody');
    
    // Load data when modal opens
    modal.addEventListener('show.bs.modal', function() {
        loadData();
    });
    
    // Save data
    saveBtn.addEventListener('click', function() {
        const year = document.getElementById('dataYear').value;
        const month = document.getElementById('dataMonth').value;
        const waterLevel = document.getElementById('waterLevel').value;
        const casualties = document.getElementById('casualties').value;
        
        const formData = new FormData();
        formData.append('action', 'save');
        formData.append('year', year);
        formData.append('month', month);
        formData.append('water_level', waterLevel);
        formData.append('casualties', casualties);
        
        fetch('includes/save_data.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                loadData();
                updateCharts(); // Refresh the charts
            } else {
                alert('Error: ' + data.message);
            }
        });
    });
    
    // New entry
    newBtn.addEventListener('click', function() {
        document.getElementById('waterLevel').value = '';
        document.getElementById('casualties').value = '';
    });
    
    // Load all data
    function loadData() {
        fetch('includes/save_data.php?action=load')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderDataTable(data.data);
            }
        });
    }
    
    // Render data table
    function renderDataTable(data) {
        dataTableBody.innerHTML = '';
        
        data.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.year}</td>
                <td>${item.month}</td>
                <td>${item.water_level}</td>
                <td>${item.casualties}</td>
                <td>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="${item.id}">Edit</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${item.id}">Delete</button>
                </td>
            `;
            dataTableBody.appendChild(row);
        });
        
        // Add event listeners to edit buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                editData(id);
            });
        });
        
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this record?')) {
                    deleteData(id);
                }
            });
        });
    }
    
    // Edit data
    function editData(id) {
        fetch('includes/save_data.php?action=load')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const item = data.data.find(i => i.id == id);
                if (item) {
                    document.getElementById('dataYear').value = item.year;
                    document.getElementById('dataMonth').value = item.month;
                    document.getElementById('waterLevel').value = item.water_level;
                    document.getElementById('casualties').value = item.casualties;
                }
            }
        });
    }
    
    // Delete data
    function deleteData(id) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('includes/save_data.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                loadData();
                updateCharts(); // Refresh the charts
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
});
</script>
</body>
</html>