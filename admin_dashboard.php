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
    <title>Dashboard</title>

    <style>
        body{
            background-color: #f0f8ff;
            color: #333;
        }
    </style>
</head>
<body>

<?php include 'includes/admin_sidebar.php'; ?>
<?php include 'includes/header.php'; ?>


<div class="container mt-4">
    <div class="row">
        <!-- Map Widget -->
        <div class="col-12 col-md-4 mb-3">
            <?php
                include 'components/map-widget.php';
                renderMapWidget();
            ?>
        </div>

        <!-- Weather Widget Column -->
        <div class="col-12 col-md-5 mb-3">
            <?php
                include 'components/weather-widget.php';
                renderWeatherWidget();
            ?>
            
            <!-- Water Level Chart Below Weather -->
            <div class="mt-3">
                <?php
                    include 'components/water-level-chart.php';
                    renderWaterLevelChart();
                ?>
            </div>
        </div>

        <!-- Water Level Widget -->
        <div class="col-12 col-md-3 mb-3">
            <?php
                include 'components/water-level-widget.php';
                renderWaterLevelWidget();
            ?>
        </div>
    </div>
</div>
    
</body>
</html>