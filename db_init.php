<?php
include 'db_config.php';

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select  database
if (!$conn->select_db($dbname)) {
    die("Database selection failed: " . $conn->error);
}

$sql = "
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    employee_id INT,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

CREATE INDEX idx_employee_id ON bookings (employee_id);
CREATE INDEX idx_event_name ON bookings (event_name);
CREATE INDEX idx_date ON bookings (date);
";

if ($conn->multi_query($sql) === TRUE) {
    echo "Database schema created successfully.";
} else {
    echo "Error creating database schema: " . $conn->error;
}

$conn->close();
?>

