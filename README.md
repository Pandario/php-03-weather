-- Use the mysql database
USE mysql;

-- Create the weather_data table if it doesn't exist
CREATE TABLE IF NOT EXISTS weather_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    maxtemp_c FLOAT NOT NULL,
    maxwind_mph FLOAT NOT NULL,
    daily_will_it_rain INT NOT NULL
);
