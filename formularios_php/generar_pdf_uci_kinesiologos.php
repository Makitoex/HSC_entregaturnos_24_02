<?php
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id'])) {
    die("No se proporcionó un ID válido.");
}

$id_formulario = $_GET['id'];

// Obtener datos del formulario desde la base de datos
$sql = "SELECT f.*, fs1.nombre_funcionarios AS funcionario_saliente_1_nombre, fs2.nombre_funcionarios AS funcionario_entrante_1_nombre 
        FROM formulario_turnos_uci_kinesiologos f 
        LEFT JOIN funcionarios_uci fs1 ON f.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_uci fs2 ON f.funcionario_entrante_1 = fs2.id_funcionarios
        WHERE f.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_formulario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No se encontró el formulario.");
}

$row = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Clase para el PDF
class PDF extends FPDF
{
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Entrega de Turno - Kinesiologos UCI - HSC', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 170, 8, 30);
        $this->Ln(15);
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Crear el PDF
$pdf = new PDF();
$pdf->SetTitle('Formulario de Turno UCI Kinesiologos');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Formato organizado en tabla
$pdf->Cell(50, 10, 'Fecha:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['fecha'], 1, 1, 'L');
$pdf->Cell(50, 10, 'Tipo de Turno:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['tipoturno'], 1, 1, 'L');
$pdf->Cell(50, 10, 'Camas Ocupadas:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['camas_ocupadas'], 1, 1, 'L');
$pdf->Cell(50, 10, 'Camas Disponibles:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['camas_disponibles'], 1, 1, 'L');
$pdf->Cell(50, 10, 'Pacientes Fallecidos:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['cant_pacientes_fallecidos'], 1, 1, 'L');
$pdf->Cell(50, 10, 'Detalles Fallecidos:', 1, 0, 'L');
$pdf->MultiCell(140, 10, $row['detalles_pacientes_fallecidos'], 1, 'L');
$pdf->Cell(50, 10, 'Eventos:', 1, 0, 'L');
$pdf->MultiCell(140, 10, $row['eventos_detalle'], 1, 'L');
$pdf->Cell(50, 10, 'Eventos Kine:', 1, 0, 'L');
$pdf->MultiCell(140, 10, $row['eventos_kine_detalle'], 1, 'L');
$pdf->Cell(50, 10, 'Comentarios:', 1, 0, 'L');
$pdf->MultiCell(140, 10, $row['comentarios_detalle'], 1, 'L');
$pdf->Cell(50, 10, 'Set Succion:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['cantidad_setsuccion'], 1, 1, 'L');
$pdf->Cell(50, 10, 'Funcionario Saliente:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['funcionario_saliente_1_nombre'], 1, 1, 'L');
$pdf->Cell(50, 10, 'Funcionario Entrante:', 1, 0, 'L');
$pdf->Cell(140, 10, $row['funcionario_entrante_1_nombre'], 1, 1, 'L');

// Agregar datos extra si es domingo
if ($row['es_domingo']) {
    // Mostrar que es domingo
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'STOCK DIA DOMINGO', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    
    // Campos adicionales para domingo
    $pdf->Cell(50, 10, 'Filtros HME:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['filtros_hme'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Filtros Antibacterianos:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['filtros_antibacterianos'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Filtros de Traqueostomia:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['filtros_traqueostomia'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Sonda Succion Cerrada:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['sonda_succion_cerrada'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Corrugado Una Rama:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['corrugado_una_rama'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Corrugado Dos Ramas:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['corrugado_dos_ramas'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Adaptador IDM:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['adaptador_idm'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Adaptador NBZ:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['adaptador_nbz'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Tubo T:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['tubo_t'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Mascarillas Talla S:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['mascarillas_talla_s'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Mascarillas Talla L:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['mascarillas_talla_l'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Mascarillas Talla XL:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['mascarillas_talla_xl'], 1, 1, 'L');
    $pdf->Cell(50, 10, 'Set Succion Unidad:', 1, 0, 'L');
    $pdf->Cell(140, 10, $row['set_succion_unidad'], 1, 1, 'L');
}

$pdf->Output();
?>
