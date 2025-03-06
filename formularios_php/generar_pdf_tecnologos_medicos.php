<?php
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');
include 'C:/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

if (!isset($_GET['id'])) {
    die("ID de formulario no proporcionado.");
}
$id_formulario = intval($_GET['id']);

$sql = "SELECT 
            t.id, t.fecha, t.tipoturno, t.rx_pendientes, t.tc_pendientes, t.portatil_pendientes, 
            t.rx_equiposoperativos, t.tc_equiposoperativos, t.portatil_equiposoperativos, 
            t.pacs_enviados, t.prueba_enviados, t.syngovia_enviados, t.codigo_carroparos, 
            t.carrodeparos, t.carroutilizado, t.salasyrx, t.inyectora, t.cd_grabados, 
            t.cd_grabadosotroturno, t.eventosadversos, t.pacientessospecha, t.novedades, 
            fs1.nombre_funcionarios AS funcionario_saliente_1, 
            fe1.nombre_funcionarios AS funcionario_entrante_1 
        FROM formulario_turnos_im_tecnologos_medicos t
        LEFT JOIN funcionarios_imagenologia fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_imagenologia fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_formulario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("No se encontró el formulario.");
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'Entrega de Turno - Tecnologos Medicos - HSC', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 160, 5, 25);
        $this->Ln(8);
    }

    function Footer() {
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 8, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, $title, 0, 1, 'L');
        $this->Ln(2);
    }

    function TableCell($label, $value) {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 6, utf8_decode($label) . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, utf8_decode($value), 1, 1, 'L');
    }

    function MultiCellRow($label, $value) {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 6, utf8_decode($label) . ':', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(0, 6, utf8_decode($value), 1, 'L');
        $this->Ln(1);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFillColor(200, 220, 255);

$pdf->SectionTitle('Información General');
$pdf->TableCell('Fecha', $row['fecha']);
$pdf->TableCell('Tipo de Turno', $row['tipoturno']);

$pdf->SectionTitle('Funcionarios');
$pdf->TableCell('Saliente', $row['funcionario_saliente_1']);
$pdf->TableCell('Entrante', $row['funcionario_entrante_1']);

$pdf->SectionTitle('Exámenes Pendientes');
$pdf->TableCell('RX', $row['rx_pendientes']);
$pdf->TableCell('TC', $row['tc_pendientes']);
$pdf->TableCell('Portátil', $row['portatil_pendientes']);

$pdf->SectionTitle('Equipos Operativos');
$pdf->TableCell('RX', $row['rx_equiposoperativos']);
$pdf->TableCell('TC', $row['tc_equiposoperativos']);
$pdf->TableCell('Portátil', $row['portatil_equiposoperativos']);

$pdf->SectionTitle('Exámenes Enviados');
$pdf->TableCell('PACS', $row['pacs_enviados']);
$pdf->TableCell('Prueba', $row['prueba_enviados']);
$pdf->TableCell('Syngovia', $row['syngovia_enviados']);

$pdf->SectionTitle('Carro de Paros');
$pdf->TableCell('Código', $row['codigo_carroparos']);
$pdf->TableCell('Abierto', $row['carrodeparos']);
$pdf->MultiCellRow('Detalles', $row['carroutilizado']);

$pdf->SectionTitle('General');
$pdf->TableCell('Salas y RX', $row['salasyrx']);
$pdf->TableCell('Inyectora', $row['inyectora']);
$pdf->TableCell('CD Grabados', $row['cd_grabados']);
$pdf->TableCell('CD en Otro Turno', $row['cd_grabadosotroturno']);
$pdf->TableCell('Eventos Adversos', $row['eventosadversos']);
$pdf->TableCell('Pacientes Sospechosos', $row['pacientessospecha']);
$pdf->MultiCellRow('Novedades', $row['novedades']);

$pdf->Output('I', 'formulario_' . $id_formulario . '.pdf');

$stmt->close();
$conn->close();
?>
