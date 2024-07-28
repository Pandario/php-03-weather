-- Use the mysql database
USE mysql;

CREATE TABLE IF NOT EXISTS weather_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forecast JSON NOT NULL
);
