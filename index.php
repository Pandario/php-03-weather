<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mysql";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$weather_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch'])) {

    $apiKey = 'c5949ab5f07f42dba76142834242807';
    $location = 'Leiderdorp';
    $days = 5;
    $apiUrl = "http://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$location&days=$days&aqi=no&alerts=no";

    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);


    $new_data = [];
    foreach ($data['forecast']['forecastday'] as $forecast) {
        $new_data[] = [
            'date' => $forecast['date'],
            'maxtemp_c' => $forecast['day']['maxtemp_c'],
            'maxwind_kph' => $forecast['day']['maxwind_kph'],
            'daily_will_it_rain' => $forecast['day']['daily_will_it_rain']
        ];
    }

 
    $_SESSION['latest_weather_data'] = $new_data;

  
    $sql = "SELECT id FROM weather_data ORDER BY id";
    $result = $conn->query($sql);
    $used_ids = [];
    while ($row = $result->fetch_assoc()) {
        $used_ids[] = $row['id'];
    }

    $new_id = 1; 
    while (in_array($new_id, $used_ids)) {
        $new_id++;
    }


    $json_data = json_encode($new_data);
    $sql = "INSERT INTO weather_data (id, forecast) VALUES ($new_id, '$json_data')";

    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // avoid resub
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Clear  sess 
if (isset($_GET['clear'])) {
    unset($_SESSION['latest_weather_data']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


if (isset($_SESSION['latest_weather_data'])) {
    $weather_data = $_SESSION['latest_weather_data'];
}

// Function to icon
function getConditionIcon($maxtemp_c, $maxwind_kph, $daily_will_it_rain) {
    $icon = '';
    if ($daily_will_it_rain) {
        $icon .= '<img src="icons/rain.png" alt="Rain">';
    } else {
        $icon .= '<img src="icons/sun.png" alt="Sun">';
    }
    if ($maxwind_kph > 15) {
        $icon .= '<img src="icons/wind.png" alt="Wind">';
    }
    if ($maxtemp_c > 24) {
        $icon .= '<img src="icons/term_red.png" alt="High Temperature">';
    } else {
        $icon .= '<img src="icons/term_blue.png" alt="Low Temperature">';
    }
    return $icon;
}


function getOverallTrendIcon($weather_data) {
    if (empty($weather_data)) {
        return '';
    }

    $first_temp = $weather_data[0]['maxtemp_c'];
    $last_temp = end($weather_data)['maxtemp_c'];

    if ($last_temp > $first_temp) {
        return '<img src="icons/happy.png" alt="Rising Temperature">';
    } elseif ($last_temp < $first_temp) {
        return '<img src="icons/sad.png" alt="Falling Temperature">';
    }
    return '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Weather App</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Greetings, let's check the weather</h1>
    <form method="POST">
        <button type="submit" name="fetch">Click me</button>
    </form>
    <form method="GET">
        <button type="submit" name="clear">Close</button>
    </form>

    <?php
    if (!empty($weather_data)) {
        echo '<div class="weather-container">';
        foreach ($weather_data as $day) {
            echo '<div class="weather-day">';
            echo '<h3>Date: ' . $day['date'] . '</h3>';
            echo '<p>Max Temp (C): ' . $day['maxtemp_c'] . '</p>';
            echo '<p>Max Wind (KPH): ' . $day['maxwind_kph'] . '</p>';
            echo '<p>Will it Rain: ' . ($day['daily_will_it_rain'] ? 'Yes' : 'No') . '</p>';
            echo getConditionIcon($day['maxtemp_c'], $day['maxwind_kph'], $day['daily_will_it_rain']);
            echo '</div>';
        }
        echo '</div>';
        echo '<div class="trend-icon">' . getOverallTrendIcon($weather_data) . '</div>';
    }
    ?>

</body>
</html>

<?php
$conn->close();
?>