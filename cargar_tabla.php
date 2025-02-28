<?php
include 'conexion.php';

$table = $_GET['table'] ?? '';

if ($table) {
    // Modifica la consulta SQL para incluir un JOIN con la tabla de usuarios
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);

    $columns = [];
    $rows = [];

    if ($result->num_rows > 0) {
        // Obtiene nombres de columnas y filtra la columna de contraseña lo que tenga contraseña se vuelve false y no muestra
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
