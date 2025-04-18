<?php
include 'conexion.php';

$table = $_GET['table'];
$search = $_GET['search'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
$offset = ($page - 1) * $rowsPerPage;

$conditions = [];
if (!empty($search)) {
    $conditions[] = "(column1 LIKE '%" . $search . "%' OR column2 LIKE '%" . $search . "%' ...)"; 
}
if (!empty($startDate)) {
    $conditions[] = "fecha >= '$startDate'"; 
}
if (!empty($endDate)) {
    $conditions[] = "fecha <= '$endDate'"; 
}

$whereClause = '';
if (count($conditions) > 0) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}


$query = "SELECT * FROM $table $whereClause ORDER BY fecha DESC LIMIT $offset, $rowsPerPage"; 
$result = $conn->query($query);

// Obtener el total de registros
$totalQuery = "SELECT COUNT(*) as total FROM $table $whereClause";

$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];

$columns = [];
$rows = [];

if ($result->num_rows > 0) {
    $columns = array_keys($result->fetch_assoc());
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $rows[] = array_values($row);
    }
}

echo json_encode([
    'columns' => $columns,
    'rows' => $rows,
    'totalRows' => $totalRows,
]);

$conn->close();
?>