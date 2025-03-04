<?php
include 'conexion.php';

$table = $_GET['table'] ?? '';
$search = $_GET['search'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

if ($table) {
    // Construye la consulta SQL con filtros
    $sql = "SELECT * FROM $table";
    $conditions = [];

    if ($search) {
        // Obtiene nombres de columnas para construir la búsqueda
        $result = $conn->query("DESCRIBE $table");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }

        // Construye la condición de búsqueda
        $searchConditions = [];
        foreach ($columns as $column) {
            $searchConditions[] = "$column LIKE '%$search%'";
        }
        $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
    }

    if ($startDate && $endDate) {
        $conditions[] = "fecha BETWEEN '$startDate' AND '$endDate'";
    }

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $result = $conn->query($sql);

    $columns = [];
    $rows = [];

    if ($result->num_rows > 0) {
        // Obtiene nombres de columnas y filtra la columna de contraseña
        $all_columns = array_keys($result->fetch_assoc());
        $columns = array_filter($all_columns, function($col) {
            return stripos($col, 'contrasena') === false;
        });

        // Restablecer puntero de resultados y obtener filas sin la columna de contraseña
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $filtered_row = [];
            foreach ($columns as $column) {
                // Si la columna es un ID de usuario, reemplázalo con el nombre del usuario
                if ($column == 'id_usuario') {
                    $usuario_id = $row[$column];
                    // Realiza una consulta para obtener el nombre del usuario
                    $user_sql = "SELECT nombre FROM usuarios WHERE id_usuarios = '$usuario_id'";
                    $user_result = $conn->query($user_sql);
                    if ($user_result->num_rows > 0) {
                        $user_row = $user_result->fetch_assoc();
                        $filtered_row[] = $user_row['nombre'];  // Agrega el nombre del usuario en lugar del ID
                    } else {
                        $filtered_row[] = ''; // Si no se encuentra el usuario, agrega un valor vacío
                    }
                } else {
                    $filtered_row[] = $row[$column];
                }
            }
            $rows[] = $filtered_row;
        }
    }

    echo json_encode(['columns' => array_values($columns), 'rows' => $rows]);
} else {
    echo json_encode(['columns' => [], 'rows' => []]);
}
?>