<?php
include 'db_config.php';

// Check taht  the database schema has been initialized
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$checkTableExistsQuery = "SHOW TABLES LIKE 'employees'";
$tableExists = $conn->query($checkTableExistsQuery)->num_rows > 0;

$conn->close();


if (!$tableExists) {
    // If the table doesn't exist, include db_init.php to create the schema
    include 'db_init.php';
} else {
    // If the table exists, show the data
    include 'data_display.php';
}
?>
