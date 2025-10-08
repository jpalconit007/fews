<?php
include('<includes/db.php');
session_start();
//Select statement
$sql = "SELECT water_level, reading_time FROM (SELECT water_level, reading_time FROM sensor_readings2 ORDER BY reading_time DESC limit 6) as water_level ORDER BY reading_time ASC ";
$result = $conn->query($sql);
$waterlevelarray = [];
if ($result->num_rows > 0) {
    // Fetch each row and store the column value in the array
    while ($row = $result->fetch_assoc()) {
        $waterlevelarray[] = $row['water_level'];
    }
}

// if ($result->num_rows > 0) {
//   // output data of each row
//   while($row = $result->fetch_assoc()) {
//     echo "id: " . $row["id"]. " - Water Level: " . $row["water_level"]. " " . $row["reading_time"]. "<br>";
//   }
// } else {
//   echo "0 results";
// }  

                                


//print_r($waterlevelarray); //test array



/**
 * Perform linear regression on a time window
 * and forecast water level after given minutes.
 *
 * @param array $times   Array of timestamps (minutes or Unix timestamps)
 * @param array $levels  Array of water levels (meters)
 * @param int   $window  Number of points for regression
 * @param int   $ahead   Forecast horizon in minutes (default: 60)
 * @return array ['slope_hr' => float, 'forecast' => float, 'intercept' => float]
 */
function regressionForecast(array $times, array $levels, int $window = 6, int $ahead = 60): array {
    $n = count($times);
    if ($n < $window) {
        return ['slope_hr' => null, 'forecast' => null, 'intercept' => null];
    }

    // Use last $window points
    $t_slice = array_slice($times, -$window);
    $h_slice = array_slice($levels, -$window);

    $m = $window;
    $mean_t = array_sum($t_slice) / $m;
    $mean_h = array_sum($h_slice) / $m;

    $num = 0.0;
    $den = 0.0;
    for ($i = 0; $i < $m; $i++) {
        $dt = $t_slice[$i] - $mean_t;
        $dh = $h_slice[$i] - $mean_h;
        $num += $dt * $dh;
        $den += $dt * $dt;
    }

    if ($den == 0) {
        return ['slope_hr' => null, 'forecast' => null, 'intercept' => null];
    }

    $slope_per_min = $num / $den;        // slope (m/min)
    $intercept     = $mean_h - $slope_per_min * $mean_t;

    // Predict future level at t_last + $ahead
    $t_future = end($t_slice) + $ahead;
    $forecast = $intercept + $slope_per_min * $t_future;

    return [
        'slope_hr'  => $slope_per_min * 60, // convert to m/hr
        'forecast'  => $forecast,           // forecasted level (m)
        'intercept' => $intercept
    ];
}

// ---------------- Example usage ----------------
$times  = [0, 5, 10, 15, 20, 25]; // minutes
$levels = [1.20, 1.23, 1.28, 1.36, 1.42, 1.50]; // meters

$result = regressionForecast($times, $waterlevelarray, 6, 60);
echo "Rate of water level increase: " . round($result['slope_hr'], 3) . " m/hr <br>";//meters 0.3048
echo "Rate of water level increase: " . round($result['slope_hr']*3.28084, 3) . " ft/hr <br>";//feet 3.28084
echo "Forecast in 1 hr: " . round($result['forecast'], 3) . " m <br>";//meters 0.3048
echo "Forecast in 1 hr: " . round($result['forecast']*3.28084, 3) . " ft <br>";//feet 3.28084
?>
