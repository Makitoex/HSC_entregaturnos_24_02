<?php
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');

// Asegúrate de que la ruta de la conexión sea correcta
include 'C:/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
// Obtener el ID del formulario desde la URL
if (!isset($_GET['id'])) {
    die("ID de formulario no proporcionado.");
}
$id_formulario = intval($_GET['id']);

// Consulta para obtener los datos del formulario con los nombres de los funcionarios
$sql = "SELECT 
            t.id,
            t.fecha,
            t.tipoturno,
            t.medico_turno,
            t.camas_ocupadas,
            t.camas_disponibles,
            t.camas_reservadas,
            t.cant_pacientes_fallecidos,
            t.detalles_pacientes_fallecidos,
            t.eventos_detalle,
            t.comentarios_detalle,
            fs1.nombre_funcionarios AS funcionario_saliente_1,
            fs2.nombre_funcionarios AS funcionario_saliente_2,
            fs3.nombre_funcionarios AS funcionario_saliente_3,
            fe1.nombre_funcionarios AS funcionario_entrante_1,
            fe2.nombre_funcionarios AS funcionario_entrante_2,
            fe3.nombre_funcionarios AS funcionario_entrante_3
        FROM formulario_turnos_uci_tens t
        LEFT JOIN funcionarios_uci fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_uci fs2 ON t.funcionario_saliente_2 = fs2.id_funcionarios
        LEFT JOIN funcionarios_uci fs3 ON t.funcionario_saliente_3 = fs3.id_funcionarios
        LEFT JOIN funcionarios_uci fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
        LEFT JOIN funcionarios_uci fe2 ON t.funcionario_entrante_2 = fe2.id_funcionarios
        LEFT JOIN funcionarios_uci fe3 ON t.funcionario_entrante_3 = fe3.id_funcionarios
        WHERE t.id = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_formulario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("No se encontró el formulario.");
}

// Crear el PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Entrega de Turno - TENS - HOSPITAL SANTA CRUZ', 0, 1, 'C');
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

    function TableCell($label, $value, $width = 50) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($width, 10, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $value, 1, 1, 'L');
    }

    function TableCellMulti($label, $value, $width = 50) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($width, 10, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $value, 1, 'L');
    }
}

// Crear una instancia del PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFillColor(200, 220, 255);

// Imprimir los datos del formulario
$pdf->ChapterTitle('Informacion General');
$pdf->TableCell('Fecha', $row['fecha']);
$pdf->TableCell('Tipo de Turno', $row['tipoturno']);

$pdf->ChapterTitle('Medicos');
$pdf->TableCell('Medico de Turno', $row['medico_turno']);

$pdf->ChapterTitle('Funcionarios Salientes');
$pdf->TableCell('Funcionario Saliente 1', $row['funcionario_saliente_1']);
$pdf->TableCell('Funcionario Saliente 2', $row['funcionario_saliente_2']);
$pdf->TableCell('Funcionario Saliente 3', $row['funcionario_saliente_3']);

$pdf->ChapterTitle('Funcionarios Entrantes');
$pdf->TableCell('Funcionario Entrante 1', $row['funcionario_entrante_1']);
$pdf->TableCell('Funcionario Entrante 2', $row['funcionario_entrante_2']);
$pdf->TableCell('Funcionario Entrante 3', $row['funcionario_entrante_3']);

$pdf->ChapterTitle('Camas');
$pdf->TableCell('Camas Ocupadas', $row['camas_ocupadas']);
$pdf->TableCell('Camas Disponibles', $row['camas_disponibles']);
$pdf->TableCell('Camas Reservadas', $row['camas_reservadas']);

$pdf->ChapterTitle('Pacientes Fallecidos');
$pdf->TableCell('C-P Fallecidos', $row['cant_pacientes_fallecidos']);
$pdf->TableCellMulti('Detalles Fallecidos', $row['detalles_pacientes_fallecidos']);

$pdf->ChapterTitle('Eventos y Comentarios');
$pdf->TableCellMulti('Eventos', $row['eventos_detalle']);
$pdf->TableCellMulti('Comentarios', $row['comentarios_detalle']);

// Enviar PDF al navegador
$pdf->Output('I', 'formulario_' . $id_formulario . '.pdf');

// Cerrar la conexión
$stmt->close();
$conn->close();
?>