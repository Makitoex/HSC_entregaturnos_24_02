<?php
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
// Obtener el ID del formulario desde la URL
if (!isset($_GET['id'])) {
    die("ID de formulario no proporcionado.");
}
$id_formulario = intval($_GET['id']);

// CONSULTA COMPLETA
$sql = "SELECT 
            f.*, 
            fs1.nombre_funcionarios AS funcionario_saliente_1, 
            fs2.nombre_funcionarios AS funcionario_saliente_2, 
            fe1.nombre_funcionarios AS funcionario_entrante_1, 
            fe2.nombre_funcionarios AS funcionario_entrante_2 
        FROM formulario_turnos_uci_enfermeros f
        LEFT JOIN funcionarios_uci fs1 ON f.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_uci fs2 ON f.funcionario_saliente_2 = fs2.id_funcionarios
        LEFT JOIN funcionarios_uci fe1 ON f.funcionario_entrante_1 = fe1.id_funcionarios
        LEFT JOIN funcionarios_uci fe2 ON f.funcionario_entrante_2 = fe2.id_funcionarios
        WHERE f.id = ?";

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
        $this->Cell(0, 10, 'Entrega de Turno - UCI - HOSPITAL SANTA CRUZ', 0, 1, 'C');
        $this->Image('C:/laragon/www/Sistema_entrega_turnos_HSC/imagen/logohsf02.jpg', 170, 8, 30);
        $this->Ln(15);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
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

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFillColor(200, 220, 255);

// ENCABEZADO DEL FORMULARIO
$pdf->ChapterTitle('Informacion General');
$pdf->TableCell('Fecha', $row['fecha']);
$pdf->TableCell('Tipo de Turno', $row['tipoturno']);
$pdf->TableCell('Funcionario Saliente', $row['funcionario_saliente_1'] . ', ' . $row['funcionario_saliente_2']);
$pdf->TableCell('Funcionario Entrante', $row['funcionario_entrante_1'] . ', ' . $row['funcionario_entrante_2']);

// DETALLES DEL FORMULARIO
$pdf->ChapterTitle('Detalles del Turno');
$pdf->TableCell('Medico de turno', $row['medico_turno']);
$pdf->TableCell('TENS de turno', $row['tens_turno']);
$pdf->TableCell('Auxiliar de turno', $row['auxiliar_turno']);
$pdf->TableCell('Kinesiologo de turno', $row['kinesiologo_turno']);
$pdf->TableCell('Control Medico', $row['controlmedico']);
$pdf->TableCell('Carro de Paros', $row['carrodeparos']);
$pdf->TableCell('Camas Ocupadas', $row['camas_ocupadas']);
$pdf->TableCell('Camas Disponibles', $row['camas_disponibles']);
$pdf->TableCell('Camas Reservadas', $row['camas_reservadas']);
$pdf->TableCell('Pacientes Fallecidos', $row['cantpacientesfallecidos']);
$pdf->TableCellMulti('Detalles Fallecidos', $row['detallespacientesf']);
$pdf->TableCellMulti('Traslados', $row['traslados_detalle']);
$pdf->TableCellMulti('Eventos', $row['eventos_detalle']);
$pdf->TableCellMulti('Comentarios', $row['comentarios_detalle']);

// Verificar si hay suficiente espacio antes de agregar el título de Medicamentos
if ($pdf->GetY() + 50 > $pdf->GetPageHeight()) {
    $pdf->AddPage();
}

// MEDICAMENTOS
$pdf->ChapterTitle('Medicamentos');
$pdf->TableCell('Ketamina', $row['medicamento_ketamina']);
$pdf->TableCell('Haldol', $row['medicamento_haldol']);
$pdf->TableCell('Diazepam 100mg EV', $row['medicamento_diazepam100ev']);
$pdf->TableCell('Diazepam 100mg VO', $row['medicamento_diazepam100vo']);
$pdf->TableCell('Rocuronio', $row['medicamento_rocuronio']);
$pdf->TableCell('Clonazepam', $row['medicamento_clonazepam']);
$pdf->TableCell('Midazolam 5mg', $row['medicamento_midazolam5mg']);
$pdf->TableCell('Midazolam 50mg', $row['medicamento_midazolam50mg']);
$pdf->TableCell('Fentanilo 0.1mg', $row['medicamento_fentanilo0_1']);
$pdf->TableCell('Fentanilo 0.5mg', $row['medicamento_fentanilo_0_5mg']);
$pdf->TableCell('Lorazepam 4mg', $row['medicamento_lorazepam_4mg']);
$pdf->TableCell('Morfina', $row['medicamento_morfina']);
$pdf->TableCell('Propofol', $row['medicamento_profolol']);
$pdf->TableCell('Quetiapina', $row['medicamento_quetiapina']);
$pdf->TableCell('Suxometonio', $row['medicamento_suxometonio']);

// ENVIAR PDF AL NAVEGADOR
$pdf->Output('I', 'formulario_' . $id_formulario . '.pdf');

// CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>