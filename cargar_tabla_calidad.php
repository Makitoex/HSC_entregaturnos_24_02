<?php
include 'conexion.php';

$table = $_GET['table'];
$search = $_GET['search'];
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
$offset = ($page - 1) * $rowsPerPage;

// Construir la consulta SQL con paginación
$query = "SELECT * FROM $table WHERE 1=1";

if ($search) {
    $query .= " AND (column1 LIKE '%$search%' OR column2 LIKE '%$search%' ...)"; // Añade aquí las columnas a buscar
}

if ($startDate && $endDate) {
    $query .= " AND date_column BETWEEN '$startDate' AND '$endDate'"; // Ajusta 'date_column' según tu base de datos
}

$query .= " LIMIT $offset, $rowsPerPage";

$result = $conn->query($query);

// Obtener el total de registros
$totalQuery = "SELECT COUNT(*) as total FROM $table WHERE 1=1";

if ($search) {
    $totalQuery .= " AND (column1 LIKE '%$search%' OR column2 LIKE '%$search%' ...)"; // Añade aquí las columnas a buscar
}

if ($startDate && $endDate) {
    $totalQuery .= " AND date_column BETWEEN '$startDate' AND '$endDate'"; // Ajusta 'date_column' según tu base de datos
}

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
?>