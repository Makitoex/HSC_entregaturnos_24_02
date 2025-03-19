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
            t.fecha,
            t.tipoturno,
            t.observaciones_equipo,
            t.tecnicas_calibradas,
            t.observaciones_quimica,
            t.quimica,
            t.hormonas,
            t.gases_elp,
            t.crd,
            t.vih_hepb,
            t.sp,
            t.mantencion,
            t.cobas_c311,
            t.cobas_c111,
            t.transfusiones,
            t.gr_0,
            t.gr_a,
            t.gr_oneg,
            t.gr_b,
            t.gr_ab,
            t.pfc_o,
            t.pfc_a,
            t.pfc_b,
            t.pfc_ab,
            t.muestras_pendientes,
            t.valores_criticos,
            t.gram_hemocultivo,
            t.gram_liquidos,
            t.paneles_pendientes,
            t.cambios_lote,
            t.insumos_criticos,
            t.pendientes_covid,
            t.funcionario_saliente,
            t.nombre_funcionario_saliente,
            t.funcionario_entrante,
            t.nombre_funcionario_entrante,
            t.contrasena_saliente,
            t.observaciones_equipo_largo,
            t.mantencion_largo,
            t.cobas_e411_largo,
            t.transfusiones_largo, 
            t.gr_0_largo,
            t.gr_a_largo,
            t.gr_oneg_largo,
            t.gr_ab_largo,
            t.pfc_o_largo,
            t.pfc_a_largo,
            t.pfc_b_largo, 
            t.pfc_ab_largo,
            t.muestras_pendientes_largo,
            t.valores_criticos_largo,
            t.gram_hemocultivo_largo, 
            t.gram_liquidos_largo,
            t.paneles_pendientes_largo,
            t.cambios_lote_largo,
            t.insumos_criticos_largo, 
            t.pendientes_covid_largo
        FROM formulario_turnos_mb_tecnologos_medicos t
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_formulario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("No se encontro el formulario.");
}

// DEBUG: Log the fetched data
error_log(print_r($row, true));

// CLASE PARA EL PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Formulario Turnos MB Tecnologos Medicos', 0, 1, 'C');
        $this->Ln(10);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
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
$pdf->TableCell('Funcionario Saliente', $row['nombre_funcionario_saliente']);
$pdf->TableCell('Funcionario Entrante', $row['nombre_funcionario_entrante']);

$pdf->ChapterTitle('Observaciones y Tecnicas');
$pdf->TableCell('Observaciones del Equipo', $row['observaciones_equipo']);
$pdf->TableCell('Tecnicas Calibradas', $row['tecnicas_calibradas']);

$pdf->ChapterTitle('Resultados de Pruebas');
$pdf->TableCell('Quimica', $row['quimica']);
$pdf->TableCell('Hormonas', $row['hormonas']);
$pdf->TableCell('Gases ELP', $row['gases_elp']);
$pdf->TableCell('CRD', $row['crd']);
$pdf->TableCell('VIH/HEPB', $row['vih_hepb']);
$pdf->TableCell('SP', $row['sp']);
$pdf->TableCell('Mantenimiento', $row['mantencion']);
$pdf->TableCell('Cobas C311', $row['cobas_c311']);
$pdf->TableCell('Cobas C111', $row['cobas_c111']);

$pdf->ChapterTitle('Transfusiones y Grupos Sanguineos');
$pdf->TableCell('Transfusiones', $row['transfusiones']);
$pdf->TableCell('Grupo 0', $row['gr_0']);
$pdf->TableCell('Grupo A', $row['gr_a']);
$pdf->TableCell('Grupo 0 Negativo', $row['gr_oneg']);
$pdf->TableCell('Grupo B', $row['gr_b']);
$pdf->TableCell('Grupo AB', $row['gr_ab']);
$pdf->TableCell('PFC 0', $row['pfc_o']);
$pdf->TableCell('PFC A', $row['pfc_a']);
$pdf->TableCell('PFC B', $row['pfc_b']);
$pdf->TableCell('PFC AB', $row['pfc_ab']);

$pdf->ChapterTitle('Muestras Pendientes y Valores Criticos');
$pdf->TableCell('Muestras Pendientes', $row['muestras_pendientes']);
$pdf->TableCell('Valores Criticos', $row['valores_criticos']);
$pdf->TableCell('Gram Hemocultivo', $row['gram_hemocultivo']);
$pdf->TableCell('Gram Liquidos', $row['gram_liquidos']);
$pdf->TableCell('Paneles Pendientes', $row['paneles_pendientes']);
$pdf->TableCell('Cambios de Lote', $row['cambios_lote']);
$pdf->TableCell('Insumos Criticos', $row['insumos_criticos']);
$pdf->TableCell('Pendientes COVID', $row['pendientes_covid']);

$pdf->ChapterTitle('Observaciones y Mantenimiento Detallados');
$pdf->MultiCellRow('Cobas E411 (Largo)', $row['cobas_e411_largo']);
$pdf->MultiCellRow('Transfusiones (Largo)', $row['transfusiones_largo']);
$pdf->MultiCellRow('Grupo 0 (Largo)', $row['gr_0_largo']);
$pdf->MultiCellRow('Grupo A (Largo)', $row['gr_a_largo']);
$pdf->MultiCellRow('Grupo 0 Negativo (Largo)', $row['gr_oneg_largo']);
$pdf->MultiCellRow('Grupo AB (Largo)', $row['gr_ab_largo']);
$pdf->MultiCellRow('PFC 0 (Largo)', $row['pfc_o_largo']);
$pdf->MultiCellRow('PFC A (Largo)', $row['pfc_a_largo']);
$pdf->MultiCellRow('PFC B (Largo)', $row['pfc_b_largo']);
$pdf->MultiCellRow('PFC AB (Largo)', $row['pfc_ab_largo']);
$pdf->MultiCellRow('Muestras Pendientes (Largo)', $row['muestras_pendientes_largo']);
$pdf->MultiCellRow('Valores Criticos (Largo)', $row['valores_criticos_largo']);
$pdf->MultiCellRow('Gram Hemocultivo (Largo)', $row['gram_hemocultivo_largo']);
$pdf->MultiCellRow('Gram Liquidos (Largo)', $row['gram_liquidos_largo']);
$pdf->MultiCellRow('Paneles Pendientes (Largo)', $row['paneles_pendientes_largo']);
$pdf->MultiCellRow('Cambios de Lote (Largo)', $row['cambios_lote_largo']);
$pdf->MultiCellRow('Insumos Criticos (Largo)', $row['insumos_criticos_largo']);
$pdf->MultiCellRow('Pendientes COVID (Largo)', $row['pendientes_covid_largo']);

// ENVIAR PDF AL NAVEGADOR
$pdf->Output('I', 'formulario_' . $id_formulario . '.pdf');

// CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>