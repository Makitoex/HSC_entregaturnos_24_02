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
            t.pendientes_quimica,
            t.pendientes_hematologia,
            t.pendientes_microbiologia,
            t.pendientes_serologia,
            t.pendientes_recepcion_muestras,
            t.tarea_hoja_trabajo,
            t.tarea_preparacion_cloro,
            t.tarea_registro_temperaturas,
            t.otras_observaciones,
            t.limpieza_quimica,
            t.limpieza_hematologia,
            t.limpieza_orina,
            t.limpieza_microbiologia,
            t.limpieza_covid,
            fs1.nombre_funcionarios AS funcionario_saliente_1,
            fe1.nombre_funcionarios AS funcionario_entrante_1
        FROM formulario_turnos_mb_tens t
        LEFT JOIN funcionarios_uci fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_uci fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_formulario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("No se encontró el formulario.");
}

// CLASE PARA EL PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Entrega de Turno - TENS Microbiologia - HSC', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 160, 8, 30);
        $this->Ln(10);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
    function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, $title, 0, 1, 'L');
        $this->Ln(2);
    }
    function TableCell($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 8, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, $value, 1, 1, 'L');
    }
    function MultiCellRow($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 8, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 8, $value, 1, 'L');
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
$pdf->TableCell('Funcionario Saliente', $row['funcionario_saliente_1']);
$pdf->TableCell('Funcionario Entrante', $row['funcionario_entrante_1']);

$pdf->ChapterTitle('Pendientes');
$pdf->MultiCellRow('P Quimica y Hormonas', $row['pendientes_quimica']);
$pdf->MultiCellRow('P Hematologia', $row['pendientes_hematologia']);
$pdf->MultiCellRow('P Microbiologia', $row['pendientes_microbiologia']);
$pdf->MultiCellRow('P Serologia y Hormonas', $row['pendientes_serologia']);
$pdf->MultiCellRow('Recepcion Muestras Deriv', $row['pendientes_recepcion_muestras']);

$pdf->ChapterTitle('Tareas a Realizar');
$pdf->TableCell('Hoja Trabajo Microbiologia', $row['tarea_hoja_trabajo']);
$pdf->TableCell('Preparacion Cloro', $row['tarea_preparacion_cloro']);
$pdf->TableCell('Registro de Temperaturas', $row['tarea_registro_temperaturas']);
$pdf->MultiCellRow('Otras Observaciones', $row['otras_observaciones']);

$pdf->ChapterTitle('Limpieza y Orden Secciones');
$pdf->TableCell('Quimica', $row['limpieza_quimica']);
$pdf->TableCell('Hematologia', $row['limpieza_hematologia']);
$pdf->TableCell('Orina', $row['limpieza_orina']);
$pdf->TableCell('Microbiologia', $row['limpieza_microbiologia']);
$pdf->TableCell('COVID', $row['limpieza_covid']);

// ENVIAR PDF AL NAVEGADOR
$pdf->Output('I', 'formulario_' . $id_formulario . '.pdf');

// CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>