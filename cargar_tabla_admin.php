<?php
include 'conexion.php';

$table = $_GET['table'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
$offset = ($page - 1) * $rowsPerPage;

// Construir la consulta SQL con paginación
$query = "SELECT * FROM $table LIMIT $offset, $rowsPerPage";
$result = $conn->query($query);

// Obtener el total de registros
$totalQuery = "SELECT COUNT(*) as total FROM $table";
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $rowsPerPage); // Calcular el número total de páginas

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
    'totalPages' => $totalPages, // Añadir el número total de páginas
]);
?>
