<?php
session_start();
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';
include 'navbar_funcionarios.php';

$id_usuario = $_SESSION['id_usuarios'];
$mensaje = "";

// Paginación
$registros_por_pagina = 10; // Cambiar este número para ajustar el número de registros por página
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener historial de reportes generados solo para el usuario actual con paginación
$sql_historial = "SELECT r.id_reporte, u.nombre AS remitente, r.reporte, r.fecha, 
                  CASE WHEN r.id_usuario_destino = 0 THEN 'Sin destinatario' ELSE ud.nombre END AS destinatario
                  FROM reportes r
                  JOIN usuarios u ON r.id_usuario_origen = u.id_usuarios
                  LEFT JOIN usuarios ud ON r.id_usuario_destino = ud.id_usuarios
                  WHERE r.id_usuario_destino = ?
                  ORDER BY r.fecha DESC
                  LIMIT ?, ?";
$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("iii", $id_usuario, $offset, $registros_por_pagina);

// Verificar errores en la ejecución de la consulta
if (!$stmt_historial->execute()) {
    die("Error en la consulta: " . $stmt_historial->error);
}
$result_historial = $stmt_historial->get_result();

// Verificar si se obtienen resultados
if ($result_historial->num_rows === 0) {
    $mensaje = "No se encontraron reportes.";
}

// Obtener el número total de reportes para la paginación
$sql_total = "SELECT COUNT(*) AS total FROM reportes WHERE id_usuario_destino = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $id_usuario);

// Verificar errores en la ejecución de la consulta
if (!$stmt_total->execute()) {
    die("Error en la consulta: " . $stmt_total->error);
}

$result_total = $stmt_total->get_result();
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes - HSC Calidad</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .container { max-width: 900px; margin-top: 50px; }
        h1 { text-align: center; color: #495057; font-family: 'Montserrat', sans-serif; margin-bottom: 30px; }
        .logo { width: 120px; display: block; margin: 0 auto 20px; }
        .table { margin-top: 20px; }
        .pagination { justify-content: center; }
        .card { box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 12px; }
        .card-header { background-color:rgb(151, 9, 9); color: white; font-size: 1.25rem; }
        .alert { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <img src="/imagen/logohsf02.jpg" alt="Logo HSF" class="logo">
        <h1>Reportes Generados al Servicio</h1>
        <div class="card">
            <div class="card-header">Historial de Reportes</div>
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-warning" role="alert">
                        <?php echo $mensaje; ?>
                    </div>
                <?php else: ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Remitente</th>
                                <th>Reporte</th>
                                <th>Fecha</th>
                                <th>Destinatario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_historial->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id_reporte']; ?></td>
                                <td><?php echo htmlspecialchars($row['remitente']); ?></td>
                                <td><?php echo htmlspecialchars($row['reporte']); ?></td>
                                <td><?php echo $row['fecha']; ?></td>
                                <td><?php echo htmlspecialchars($row['destinatario']); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Paginación -->
        <nav>
            <ul class="pagination mt-4">
                <?php if ($pagina_actual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>" aria-label="Siguiente">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>