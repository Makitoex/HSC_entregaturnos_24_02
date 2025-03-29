<?php
session_start();

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir la biblioteca FPDF
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');

// Asegúrate de que la ruta de la conexión sea correcta
include 'C:/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Obtener el ID del formulario desde la URL
if (!isset($_GET['id'])) {
    die("ID de formulario no proporcionado.");
}
$id_formulario = intval($_GET['id']);

// Consulta para obtener los datos del formulario
$sql = "SELECT 
            fecha, tipoturno, horario, nombre_tens_sala, 
            paciente_1, paciente_2, paciente_3, paciente_4, paciente_5, paciente_6, 
            novedades, equipos_prestamo, servicio, observaciones, 
            nombre_funcionario_saliente_1, nombre_funcionario_entrante_1, 
            saturometros, alcoholes, pendrive_pediatria, tablillas, libros, pecheras, 
            rotulos, material_esteril, fichas_medicas, actualizacion_egresos, carro_insumos, salas_con_epp,
            banos, pisos, da_aviso, chatas, aseo_terminal
        FROM formulario_turnos_pd_tens_pediatria
        WHERE id = ?";

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
        $this->Cell(0, 8, 'Entrega de Turno - TENS Pediatria - HSC', 0, 1, 'C');
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
$pdf->TableCell('Horario', $row['horario']);
$pdf->TableCell('Nombre TENS Sala', $row['nombre_tens_sala']);

$pdf->ChapterTitle('Funcionarios');
$pdf->TableCell('Funcionario Saliente', $row['nombre_funcionario_saliente_1']);
$pdf->TableCell('Funcionario Entrante', $row['nombre_funcionario_entrante_1']);

$pdf->ChapterTitle('Resumen Evolucion Pacientes');
$pdf->TableCell('96-1', $row['paciente_1']);
$pdf->TableCell('96-2', $row['paciente_2']);
$pdf->TableCell('96-3', $row['paciente_3']);
$pdf->TableCell('114-1', $row['paciente_4']);
$pdf->TableCell('114-2', $row['paciente_5']);
$pdf->TableCell('114-3', $row['paciente_6']);

$pdf->ChapterTitle('Novedades');
$pdf->MultiCellRow('Novedades', $row['novedades']);

$pdf->ChapterTitle('Equipos en Prestamo');
$pdf->TableCell('Equipos en Prestamo', $row['equipos_prestamo']);
$pdf->TableCell('Servicio', $row['servicio']);

$pdf->ChapterTitle('Observaciones');
$pdf->MultiCellRow('Observaciones', $row['observaciones']);

$pdf->ChapterTitle('Insumos y Equipos');
$pdf->TableCell('Saturometros', $row['saturometros']);
$pdf->TableCell('Alcoholes', $row['alcoholes']);
$pdf->TableCell('Pendrive Pediatria', $row['pendrive_pediatria']);
$pdf->TableCell('Tablillas', $row['tablillas']);
$pdf->TableCell('Libros', $row['libros']);
$pdf->TableCell('Pecheras', $row['pecheras']);
$pdf->TableCell('Rotulos', $row['rotulos']);
$pdf->TableCell('Material Esteril', $row['material_esteril']);
$pdf->TableCell('Fichas Medicas', $row['fichas_medicas']);
$pdf->TableCell('Actualizacion Egresos', $row['actualizacion_egresos']);
$pdf->TableCell('Carro Insumos', $row['carro_insumos']);
$pdf->TableCell('Salas con EPP', $row['salas_con_epp']);

$pdf->ChapterTitle('Aseo');
$pdf->TableCell('Baños', $row['banos']);
$pdf->TableCell('Pisos', $row['pisos']);
$pdf->TableCell('Da Aviso', $row['da_aviso']);
$pdf->TableCell('Chatas', $row['chatas']);
$pdf->TableCell('Aseo Terminal', $row['aseo_terminal']);

// ENVIAR PDF AL NAVEGADOR
$pdf->Output('I', 'formulario_tens_pediatria_' . $id_formulario . '.pdf');

// CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>