<?php
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
$table = $_GET['table'] ?? 'formulario_turnos_uti_enfermeros';

// Validar la tabla seleccionada para evitar inyecciones SQL
$valid_tables = ['formulario_turnos_uti_enfermeros', 'formulario_turnos_uci_enfermeros', 'formulario_turnos_upc_medicos'];
if (!in_array($table, $valid_tables)) {
    die('Tabla no válida.');
}

// Consultar los datos de la tabla seleccionada
$query = "SELECT fecha, COUNT(*) as turnos FROM $table GROUP BY fecha";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$turnos = [];
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['fecha'];
    $turnos[] = $row['turnos'];
}

// Simulación de datos de turnos entrantes para el ejemplo
$entrantes = array_map(function($turno) {
    return $turno * rand(1, 3);
}, $turnos);

echo json_encode([
    'labels' => $labels,
    'turnos' => $turnos,
    'entrantes' => $entrantes,
]);

$conn->close();
?>
