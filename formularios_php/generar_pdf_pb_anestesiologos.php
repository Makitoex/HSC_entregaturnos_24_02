<?php
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');

// Asegúrate de que la ruta de la conexión sea correcta
include 'C:/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Obtener el ID del formulario desde la URL
if (!isset($_GET['id'])) {
    die("ID de formulario no proporcionado.");
}
$id_formulario = intval($_GET['id']);

// Consulta SQL adaptada a la nueva tabla
$sql = "SELECT 
            t.id,
            t.fecha,
            t.tipoturno,
            t.nombre1,
            t.detalles1,
            t.peridural1,
            t.cateter_reg1,
            t.analgev1,
            t.otro1,
            t.nombre2,
            t.detalles2,
            t.peridural2,
            t.cateter_reg2,
            t.analgev2,
            t.otro2,
            t.nombre3,
            t.detalles3,
            t.peridural3,
            t.cateter_reg3,
            t.analgev3,
            t.otro3,
            t.nombre4,
            t.detalles4,
            t.peridural4,
            t.cateter_reg4,
            t.analgev4,
            t.otro4,
            t.nombre5,
            t.detalles5,
            t.peridural5,
            t.cateter_reg5,
            t.analgev5,
            t.otro5,
            t.nombre6,
            t.detalles6,
            t.peridural6,
            t.cateter_reg6,
            t.analgev6,
            t.otro6,
            t.nombre7,
            t.detalles7,
            t.peridural7,
            t.cateter_reg7,
            t.analgev7,
            t.otro7,
            t.funcionario_saliente_1,
            t.nombre_funcionario_saliente_1,
            t.pin_funcionario_saliente_1,
            t.contrasena_saliente_1,
            t.funcionario_entrante_1,
            t.nombre_funcionario_entrante_1,
            t.pin_funcionario_entrante_1
        FROM formulario_turnos_pb_anestesistas t
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_formulario);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró el formulario
if ($result->num_rows > 0) {
    // Obtener los datos del formulario
    $row = $result->fetch_assoc();
} else {
    die("No se encontró el formulario con el ID: $id_formulario.");
}

// CLASE PARA EL PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Formulario Turno Anestesistas', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 160, 10, 30);
        $this->Ln(20);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
    function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $title, 0, 1, 'L');
        $this->Ln(5);
    }
    function TableCell($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 10, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, $value, 1, 1, 'L');
    }
    function MultiCellRow($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 10, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 10, $value, 1, 'L');
        $this->Ln(5);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFillColor(200, 220, 255);

// ENCABEZADO DEL FORMULARIO
$pdf->ChapterTitle('Informacion General');
$pdf->TableCell('Fecha', $row['fecha']);
$pdf->TableCell('Tipo de Turno', $row['tipoturno']);

// Información de los pacientes
$pdf->ChapterTitle('Pacientes');
for ($i = 1; $i <= 7; $i++) {
    $pdf->TableCell('Nombre ' . $i, $row['nombre' . $i]);
    $pdf->MultiCellRow('Detalles ' . $i, $row['detalles' . $i]);
    $pdf->TableCell('Peridural ' . $i, $row['peridural' . $i]);
    $pdf->TableCell('Cateter Reg. ' . $i, $row['cateter_reg' . $i]);
    $pdf->TableCell('Analgesia ' . $i, $row['analgev' . $i]);
    $pdf->TableCell('Otro ' . $i, $row['otro' . $i]);
    $pdf->Ln(5);
}

// Información de los funcionarios
$pdf->ChapterTitle('Funcionarios');
$pdf->TableCell('Funcionario Saliente 1', $row['nombre_funcionario_saliente_1']);
$pdf->TableCell('Funcionario Entrante 1', $row['nombre_funcionario_entrante_1']);
// Generar el PDF
$pdf->Output();
?>