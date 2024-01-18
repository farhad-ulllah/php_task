<?php
include 'db_config.php';
$jsonData = '{"bookings": [{"employee": "Farhad", "event": "Event 1", "date": "2024-01-18", "price": 50.00},
                           {"employee": "khan", "event": "Event 2", "date": "2024-01-19", "price": 75.00},
                           {"employee": "ahmad", "event": "Event 3", "date": "2024-01-20", "price": 100.00}]}';

$data = json_decode($jsonData, true);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$employeeNames = [];

foreach ($data['bookings'] as $booking) {
    $employeeNames[] = $conn->real_escape_string($booking['employee']);
}

// insert employees
$employeeValues = implode("'),('", $employeeNames);
$conn->query("INSERT INTO employees (name) VALUES ('$employeeValues') 
              ON DUPLICATE KEY UPDATE name=name");

foreach ($data['bookings'] as $booking) {
    $employeeName = $booking['employee'];
    $employeeId = getEmployeeId($conn, $employeeName);

    insertBooking($conn, $booking['event'], $booking['date'], $booking['price'], $employeeId);
}

function getEmployeeId($conn, $name) {
    $name = $conn->real_escape_string($name);
    $result = $conn->query("SELECT id FROM employees WHERE name = '$name'");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {
        return null; // Handle the case where the employee is not found
    }
}

function insertBooking($conn, $eventName, $date, $price, $employeeId) {
    $eventName = $conn->real_escape_string($eventName);
    $date = $conn->real_escape_string($date);
    $price = (float) $price;

    $conn->query("INSERT INTO bookings (event_name, date, price, employee_id) 
                  VALUES ('$eventName', '$date', $price, $employeeId)");
}

$conn->close();

header("Location: index.php");
exit;
?>
