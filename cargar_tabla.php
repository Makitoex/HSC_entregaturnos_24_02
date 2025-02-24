<?php
include 'conexion.php';

$table = $_GET['table'] ?? '';
$search = $_GET['search'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

if ($table) {
    // Prepara la consulta base
    $sql = "SELECT * FROM $table";
    
    // Si la tabla es una tabla de formulario, se agrega el JOIN correspondiente
    if (strpos($table, 'formulario_turnos_uci') !== false) {
        $sql = "SELECT t.*, 
                        f.nombre_funcionarios AS nombre_funcionario_saliente_1, 
                        f2.nombre_funcionarios AS nombre_funcionario_saliente_2,
                        e1.nombre_funcionarios AS nombre_funcionario_entrante_1, 
                        e2.nombre_funcionarios AS nombre_funcionario_entrante_2
                FROM $table AS t 
                LEFT JOIN funcionarios_uci AS f ON t.funcionario_saliente_1 = f.id_funcionarios
                LEFT JOIN funcionarios_uci AS f2 ON t.funcionario_saliente_2 = f2.id_funcionarios
                LEFT JOIN funcionarios_uci AS e1 ON t.funcionario_entrante_1 = e1.id_funcionarios
                LEFT JOIN funcionarios_uci AS e2 ON t.funcionario_entrante_2 = e2.id_funcionarios";
    } elseif (strpos($table, 'formulario_turnos_uti') !== false) {
        $sql = "SELECT t.*, 
                        f.nombre_funcionarios AS nombre_funcionario_saliente_1, 
                        f2.nombre_funcionarios AS nombre_funcionario_saliente_2,
                        e1.nombre_funcionarios AS nombre_funcionario_entrante_1, 
                        e2.nombre_funcionarios AS nombre_funcionario_entrante_2
                FROM $table AS t 
                LEFT JOIN funcionarios_uti AS f ON t.funcionario_saliente_1 = f.id_funcionarios
                LEFT JOIN funcionarios_uti AS f2 ON t.funcionario_saliente_2 = f2.id_funcionarios
                LEFT JOIN funcionarios_uti AS e1 ON t.funcionario_entrante_1 = e1.id_funcionarios
                LEFT JOIN funcionarios_uti AS e2 ON t.funcionario_entrante_2 = e2.id_funcionarios";
    } elseif (strpos($table, 'formulario_turnos_uti_kinesiologos') !== false) {
        $sql = "SELECT t.*, 
                f.nombre_funcionarios AS nombre_funcionario_saliente_1, 
                e1.nombre_funcionarios AS nombre_funcionario_entrante_1 
        FROM $table AS t 
        LEFT JOIN funcionarios_uti_kinesiologos AS f ON t.funcionario_saliente_1 = f.id_funcionarios
        LEFT JOIN funcionarios_uti_kinesiologos AS e1 ON t.funcionario_entrante_1 = e1.id_funcionarios";
    }

    // Si hay un término de búsqueda, se agrega a la consulta
    if ($search) {
        $sql .= " WHERE CONCAT_WS(' ', " . implode(", ", obtenerColumnas($conn, $table)) . ") LIKE '%$search%'";
    }

    // Si hay un rango de fechas, se agrega a la consulta
    if ($startDate && $endDate) {
        if (strpos($sql, "WHERE") !== false) {
            $sql .= " AND fecha BETWEEN '$startDate' AND '$endDate'";
        } else {
            $sql .= " WHERE fecha BETWEEN '$startDate' AND '$endDate'";
        }
    }

    $result = $conn->query($sql);

    $columns = [];
    $rows = [];

    if ($result->num_rows > 0) {
        // Obtiene los nombres de las columnas y filtra la columna de contraseña
        $all_columns = array_keys($result->fetch_assoc());
        $columns = array_filter($all_columns, function($col) {
            return stripos($col, 'contrasena') === false;
        });

        // Restablece el puntero de resultados y obtiene las filas sin la columna de contraseña
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $filtered_row = [];
            foreach ($columns as $column) {
                $filtered_row[] = $row[$column];
            }
            $rows[] = $filtered_row;
        }
    }

    echo json_encode(['columns' => array_values($columns), 'rows' => $rows]);
} else {
    echo json_encode(['columns' => [], 'rows' => []]);
}

// Función auxiliar para obtener las columnas de una tabla
function obtenerColumnas($conexion, $tabla) {
    $query = "DESCRIBE $tabla";
    $resultado = mysqli_query($conexion, $query);
    $columnas = [];
    while ($columna = mysqli_fetch_assoc($resultado)) {
        $columnas[] = $columna['Field'];
    }
    return $columnas;
}
?>
