<?php
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');

// Asegúrate de que la ruta de la conexión sea correcta
include 'C:/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Obtener el ID del formulario desde la URL
if (!isset($_GET['id'])) {
    die("ID de formulario no proporcionado.");
}
$id_formulario = intval($_GET['id']);

// CONSULTA COMPLETA
$sql = "SELECT 
            t.id,
            t.fecha,
            t.tipoturno,
            t.nombre_anestesiologo_turno,
            t.nombre_enfermera_turno,
            t.nombre_tecanestesia_turno,
            t.reposicion_peridural,
            t.reposicion_carro_anestesia,
            t.eliminacion_medicamentos,
            t.novedades,
            t.arsenalero,
            t.insumos_empresa_externa,
            t.stock_instrumental,
            t.novedades_instrumental,
            t.pabellonero,
            t.reposicion_carro_recuperacion,
            t.cambio_humidificadores,
            t.revision_temperaturas_pabellones,
            t.pacientes_cma,
            t.biopsias_ordenes_recibidas,
            t.biopsias_ordenes_entregadas,
            t.limpieza_pyxis,
            t.limpieza_bodegas,
            t.nombre_funcionario_tecanestesia,
            t.nombre_funcionario_arsenalero,
            t.nombre_funcionario_pabellonero
        FROM formulario_turnos_pb_tens t
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_formulario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("No se encontró el formulario.");
}

// DEBUG: Log the fetched data
error_log(print_r($row, true));

// CLASE PARA EL PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, 'Entrega de Turno -  TENS Pabellon - HSC', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 160, 8, 30);
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 8, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
    function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 6, $title, 0, 1, 'L');
        $this->Ln(1);
    }
    function TableCell($label, $value) {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(50, 6, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 6, $value, 1, 1, 'L');
    }
    function MultiCellRow($label, $value) {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(50, 6, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(0, 6, $value, 1, 'L');
        $this->Ln(1);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFillColor(200, 220, 255);

// ENCABEZADO DEL FORMULARIO
$pdf->ChapterTitle('Informacion General');
$pdf->TableCell('Fecha', $row['fecha']);
$pdf->TableCell('Tipo de Turno', $row['tipoturno']);

$pdf->ChapterTitle('Funcionarios');
$pdf->TableCell('Anestesiologo de Turno', $row['nombre_anestesiologo_turno']);
$pdf->TableCell('Enfermera de Turno', $row['nombre_enfermera_turno']);
$pdf->TableCell('Tec. Anestesia de Turno', $row['nombre_tecanestesia_turno']);
$pdf->TableCell('Arsenalero de Turno', $row['arsenalero']);
$pdf->TableCell('Pabellonero de Turno', $row['pabellonero']);
$pdf->TableCell('Arsenalero Entrante', $row['nombre_funcionario_arsenalero']);
$pdf->TableCell('Tec.Anestesia Entrante', $row['nombre_funcionario_tecanestesia']);
$pdf->TableCell('Pabellonero Entrante', $row['nombre_funcionario_pabellonero']);

$pdf->ChapterTitle('Reposiciones y Eliminaciones');
$pdf->TableCell('R bandeja de Peridural', $row['reposicion_peridural']);
$pdf->TableCell('R carro de Anestesia', $row['reposicion_carro_anestesia']);
$pdf->TableCell('E. M sobrantes de bandeja', $row['eliminacion_medicamentos']);

$pdf->ChapterTitle('Novedades');
$pdf->MultiCellRow('Novedades', $row['novedades']);
$pdf->MultiCellRow('Novedades Instrumental', $row['novedades_instrumental']);

$pdf->ChapterTitle('Insumos y Stock');
$pdf->TableCell('Insumos instrumental EM EX', $row['insumos_empresa_externa']);
$pdf->TableCell('Stock para tabla quirurgica', $row['stock_instrumental']);

$pdf->ChapterTitle('Revisiones');
$pdf->TableCell('R carro de recuperacion', $row['reposicion_carro_recuperacion']);
$pdf->TableCell('Cambio de humidificadores', $row['cambio_humidificadores']);
$pdf->TableCell('Registro temperaturas de PB', $row['revision_temperaturas_pabellones']);
$pdf->TableCell('Pacientes ingresados CMA', $row['pacientes_cma']);

$pdf->ChapterTitle('Biopsias');
$pdf->TableCell('Biopsias y Ordenes R', $row['biopsias_ordenes_recibidas']);
$pdf->TableCell('Biopsias y Ordenes E', $row['biopsias_ordenes_entregadas']);

$pdf->ChapterTitle('Limpieza');
$pdf->TableCell('Limpieza del Pyxis', $row['limpieza_pyxis']);
$pdf->TableCell('Limpieza de Bodegas', $row['limpieza_bodegas']);

// ENVIAR PDF AL NAVEGADOR
$pdf->Output('I', 'formulario_pabellon_' . $id_formulario . '.pdf');

// CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>