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
            t.anestesiologo_turno,
            t.tens_turno,
            t.biopsias_recibidas,
            t.biopsias_entregadas,
            t.revision_carro_paro,
            t.revision_carro_recuperacion,
            t.stock_minimo_medicamentos,
            t.stock_minimo_insumos,
            t.limpieza_pyxis,
            t.limpieza_bodegas,
            t.registro_temperatura_refrigerador,
            t.registro_temperatura_ambiental,
            t.novedades,
            t.pendientes,
            fs1.nombre_funcionarios AS nombre_funcionario_saliente_1,
            fe1.nombre_funcionarios AS nombre_funcionario_entrante_1
        FROM formulario_turnos_pb_enfermeros t
        LEFT JOIN funcionarios_pabellon fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_pabellon fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
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
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Entrega de Turno -  Enfermeros Pabellon - HSC', 0, 1, 'C');
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
$pdf->TableCell('Funcionario Saliente', $row['nombre_funcionario_saliente_1']);
$pdf->TableCell('Funcionario Entrante', $row['nombre_funcionario_entrante_1']);

$pdf->ChapterTitle('Biopsias');
$pdf->TableCell('Biopsias Recibidas', $row['biopsias_recibidas']);
$pdf->TableCell('Biopsias Entregadas', $row['biopsias_entregadas']);

$pdf->ChapterTitle('Revisiones');
$pdf->TableCell('Revision Carro de Paro', $row['revision_carro_paro']);
$pdf->TableCell('Rev. Carro de Recuperacion', $row['revision_carro_recuperacion']);
$pdf->TableCell('Stock Min Medicamentos', $row['stock_minimo_medicamentos']);
$pdf->TableCell('Stock Min Insumos', $row['stock_minimo_insumos']);

$pdf->ChapterTitle('Limpieza');
$pdf->TableCell('Limpieza Pyxis', $row['limpieza_pyxis']);
$pdf->TableCell('Limpieza Bodegas', $row['limpieza_bodegas']);

$pdf->ChapterTitle('Registro de Temperaturas');
$pdf->TableCell('Temperatura Refrigerador', $row['registro_temperatura_refrigerador']);
$pdf->TableCell('Temperatura Ambiental', $row['registro_temperatura_ambiental']);

$pdf->ChapterTitle('Comentarios');
$pdf->MultiCellRow('Novedades', $row['novedades']);
$pdf->MultiCellRow('Pendientes', $row['pendientes']);

// ENVIAR PDF AL NAVEGADOR
$pdf->Output('I', 'formulario_pabellon_' . $id_formulario . '.pdf');

// CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>