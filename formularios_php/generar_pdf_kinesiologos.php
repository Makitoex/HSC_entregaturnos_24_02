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
            t.camas_ocupadas,
            t.camas_disponibles,
            t.cant_pacientes_fallecidos,
            t.detalles_pacientes_fallecidos,
            t.eventos_detalle,
            t.acv_detalle,
            t.cantidad_setsuccion,
            t.Eventoskine_detalle,
            t.comentarios_detalle,
            fs1.nombre_funcionarios AS funcionario_saliente_1,
            fe1.nombre_funcionarios AS funcionario_entrante_1
        FROM formulario_turnos_uti_kinesiologos t
        LEFT JOIN funcionarios_uti fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_uti fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
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
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Entrega de Turno - Kinesiologos - HSC', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 170, 8, 30);
        $this->Ln(15);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
    function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $title, 0, 1, 'L');
        $this->Ln(4);
    }
    function TableCell($label, $value) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50, 10, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $value, 1, 1, 'L');
    }
    function MultiCellRow($label, $value) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50, 10, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $value, 1, 'L');
        $this->Ln(2);
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

$pdf->ChapterTitle('Camas');
$pdf->TableCell('Camas Ocupadas', $row['camas_ocupadas']);
$pdf->TableCell('Camas Disponibles', $row['camas_disponibles']);

$pdf->ChapterTitle('Pacientes Fallecidos');
$pdf->TableCell('Cantidad de Fallecidos', $row['cant_pacientes_fallecidos']);
$pdf->MultiCellRow('Detalles de Fallecidos', $row['detalles_pacientes_fallecidos']);

$pdf->ChapterTitle('Eventos y Comentarios');
$pdf->MultiCellRow('Eventos', $row['eventos_detalle']);
$pdf->MultiCellRow('ACV derivados a APS', $row['acv_detalle']);
$pdf->TableCell('Set de Succion x UN', $row['cantidad_setsuccion']);
$pdf->MultiCellRow('EA de Kinesioterapia', $row['Eventoskine_detalle']);
$pdf->MultiCellRow('Comentarios', $row['comentarios_detalle']);

// ENVIAR PDF AL NAVEGADOR
$pdf->Output('I', 'formulario_' . $id_formulario . '.pdf');

// CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>
