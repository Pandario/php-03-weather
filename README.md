We fetching data, from weatherapi. It stores in table each time we click btn 'click me'.

If rainy - shows rain/not rainy - shows sun. If wind more 15km\h it will show wind icon as well.
If temp less than 24C will show cold. If more - red termometr.
If on the last day temp will be higher than on first day - app will show happy smile. and sad one if temp will be less.

-- Use the mysql database
USE mysql;

CREATE TABLE IF NOT EXISTS weather_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forecast JSON NOT NULL
);
