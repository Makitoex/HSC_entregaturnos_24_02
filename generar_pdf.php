<?php
require('fpdf/fpdf.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['table']) || !isset($data['columns']) || !isset($data['data']) || empty($data['data'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit();
}

$tableName = $data['table'];
$columns = $data['columns'];
$rowData = $data['data'];

// Crear una instancia de FPDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, 'Reporte de Tabla - HOSPITAL SANTA CRUZ', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 170, 8, 30);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, $title, 0, 1, 'L');
        $this->Ln(4);
    }

    function TableCell($label, $value, $width = 50) {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($width, 6, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 6, $value, 1, 1, 'L');
    }

    function TableCellMulti($label, $value, $width = 50) {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($width, 6, $label . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(0, 6, $value, 1, 'L');
    }
}

// Crear una instancia del PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFillColor(200, 220, 255);

// Imprimir los datos del formulario
$pdf->ChapterTitle('Informacion General');
$pdf->TableCell('Tabla', str_replace('_', ' ', ucwords($tableName)));

// Agregar los datos de forma organizada
foreach ($rowData as $key => $value) {
    $columnName = $columns[$key];
    // Títulos de columnas en negrita y mayor tamaño
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(50, 6, "$columnName:", 1, 0, 'L', true);
    // Valores en formato normal con mayor espacio
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell(0, 6, $value, 1, 'L');
    // Línea de separación entre filas
    $pdf->Ln(3); // Espacio entre filas para dar aire
}

// Enviar el PDF al navegador
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="registro.pdf"');
$pdf->Output();
?>