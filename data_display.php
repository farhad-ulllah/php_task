<?php
include 'db_config.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination settings
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Fetch data from the database based on filters with pagination
$employeeFilter = isset($_GET['employee']) ? $conn->real_escape_string($_GET['employee']) : '';
$eventFilter = isset($_GET['event']) ? $conn->real_escape_string($_GET['event']) : '';
$dateFilter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

$filterConditions = [];

if ($employeeFilter !== '') {
    $filterConditions[] = "employees.name = '$employeeFilter'";
}

if ($eventFilter !== '') {
    $filterConditions[] = "bookings.event_name = '$eventFilter'";
}

if ($dateFilter !== '') {
    $filterConditions[] = "bookings.date = '$dateFilter'";
}

$filterQuery = implode(' AND ', $filterConditions);

$sql = "
SELECT employees.name AS employee_name, bookings.event_name, bookings.date, bookings.price
FROM bookings
JOIN employees ON bookings.employee_id = employees.id
" . ($filterQuery ? "WHERE $filterQuery" : "") . "
LIMIT $recordsPerPage OFFSET $offset;
";

$result = $conn->query($sql);

echo '<html>';
echo '<head>';
echo '<title>Booking System</title>';
echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">';
echo '</head>';
echo '<body class="container mt-5">';
echo '<div class="d-flex justify-content-between mb-4">';
echo '  <h2 class="mb-0">Filtered Results</h2>';
echo '  <a href="data_insert.php" class="btn btn-success">Add JSON Data</a>';
echo '</div>';
echo '<form action="index.php" method="get" class="mb-4">';
echo '<div class="form-row">';
echo '  <div class="col-md-3 mb-3">';
echo '    <label for="employee">Employee Name</label>';
echo '    <input type="text" class="form-control" name="employee" id="employee">';
echo '  </div>';
echo '  <div class="col-md-3 mb-3">';
echo '    <label for="event">Event Name</label>';
echo '    <input type="text" class="form-control" name="event" id="event">';
echo '  </div>';
echo '  <div class="col-md-3 mb-3">';
echo '    <label for="date">Date</label>';
echo '    <input type="date" class="form-control" name="date" id="date">';
echo '  </div>';
echo '  <div class="col-md-3 mb-3">';
echo '    <label></label>';
echo '    <button class="btn btn-primary" type="submit">Filter</button>';
echo '  </div>';
echo '</div>';
echo '</form>';
echo '<table class="table table-bordered">';
echo '<thead class="thead-dark">';
echo '<tr><th>Employee Name</th><th>Event Name</th><th>Date</th><th>Price</th></tr></thead>';
echo '<tbody>';

$totalPrice = 0;

while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['employee_name']}</td><td>{$row['event_name']}</td><td>{$row['date']}</td><td>{$row['price']}</td></tr>";
    $totalPrice += $row['price'];
}

echo '</tbody>';
echo '<tfoot>';
echo '  <tr>';
echo '    <td colspan="3">Total Price</td>';
echo '    <td>' . number_format($totalPrice, 2) . '</td>';
echo '  </tr>';
echo '</tfoot>';
echo '</table>';

// Pagination links
$totalRecords = $conn->query("SELECT COUNT(*) AS total FROM bookings JOIN employees ON bookings.employee_id = employees.id" . ($filterQuery ? " WHERE $filterQuery" : ""))->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

echo '<ul class="pagination">';
for ($i = 1; $i <= $totalPages; $i++) {
    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
}
echo '</ul>';

echo '</body>';
echo '</html>';

$conn->close();
?>
