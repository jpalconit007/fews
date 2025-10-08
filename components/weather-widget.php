<?php
function renderWeatherWidget() {
    $apiKey = "9ddde3c10f92faf33793ea3f68f27b63"; // Replace with your API key
    $city = "Doña Imelda, PH"; // Your city
    $city = urlencode($city); // Encode spaces and special characters
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";

    // Fetch weather data
    $response = @file_get_contents($apiUrl);

    if ($response === FALSE) {
        echo "<p>Error fetching weather data. Please try again later.</p>";
        return;
    }

    $data = json_decode($response, true);

    if ($data && $data['cod'] == 200) {
        $temperature = $data['main']['temp'];
        $humidity = $data['main']['humidity'];
        $windSpeed = $data['wind']['speed'];
        $weatherDescription = ucfirst($data['weather'][0]['description']);
        $weatherIcon = $data['weather'][0]['icon'];
    } else {
        $temperature = "N/A";
        $humidity = "N/A";
        $windSpeed = "N/A";
        $weatherDescription = "N/A";
        $weatherIcon = "01d"; // Default icon
    }

    ?>
<div class="col-12">
    <div class="dashboard-card">
        <div class="weather-card-content">
            <div class="weather-text-section">
                <h5><strong>Live Weather Data of Doña Imelda</strong></h5>
                <div class="weather-metrics-container">
                    <div class="metric-row">
                        <div class="weather-metric">
                            <i class="fas fa-thermometer-half"></i>
                            <span id="temp"><?php echo $temperature; ?></span>°C
                        </div>
                        <div class="weather-metric">
                            <i class="fas fa-tint"></i>
                            <span id="humidity"><?php echo $humidity; ?></span>%
                        </div>
                        <div class="weather-metric">
                            <i class="fas fa-wind"></i>
                            <span id="wind"><?php echo $windSpeed; ?></span> km/h
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="weather-icon-section">
                <img src="https://openweathermap.org/img/wn/<?php echo $weatherIcon; ?>@2x.png" alt="Weather Icon">
                <p class="weather-info"><strong>Condition:</strong> <span id="description"><?php echo $weatherDescription; ?></span></p>
            </div>
        </div>
    </div>
</div>

    <?php
}
?>