<?php
require('C:/laragon/www/Sistema_entrega_turnos_HSC/fpdf/fpdf.php');

// Asegúrate de que la ruta de la conexión sea correcta
include 'C:/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Obtener el ID del formulario desde la URL
if (!isset($_GET['id'])) {
    die("ID de formulario no proporcionado.");
}
$id_formulario = intval($_GET['id']);

// Obtener los datos del formulario
$sql = "SELECT 
            t.id,
            t.fecha,
            t.hora,
            t.cama_hospitalizado,
            t.nombre_hospitalizado,
            t.edad_hospitalizado,
            t.eih_hospitalizado,
            t.diagnosticos_hospitalizado,
            t.novedades_hospitalizado,
            t.planes_hospitalizado,
            t.nombre_egresado,
            t.destino_egresado,
            t.motivo_de_egreso,
            t.nombre_fallecido,
            t.edad_fallecido,
            t.hora_fallecido,
            t.diagnosticos_fallecido,
            t.servicio_fallecido,
            t.nombre_rechazadas,
            t.rut_rechazado,
            t.diagnostico_rechazado,
            t.servicio_rechazado,
            t.motivo_rechazo,
            t.especialidad_saliente,
            t.especialidad_entrante,
            fs1.nombre_funcionarios AS nombre_funcionario_saliente_1,
            fe1.nombre_funcionarios AS nombre_funcionario_entrante_1
        FROM formulario_turnos_upc_medicos t
        LEFT JOIN funcionarios_uti fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
        LEFT JOIN funcionarios_uti fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
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
        $this->Cell(0, 10, 'Entrega Turno UPC Medicos - HOSPITAL SANTA CRUZ', 0, 1, 'C');
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
}

// Crear una instancia del PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFillColor(200, 220, 255);

// Imprimir los datos del formulario
$pdf->ChapterTitle('Informacion General');
$pdf->TableCell('Fecha', $row['fecha']);
$pdf->TableCell('Hora de entrega', $row['hora']);

$pdf->ChapterTitle('Medicos');
$pdf->TableCell('Medico Saliente 1', $row['nombre_funcionario_saliente_1']);
$pdf->TableCell('Especialidad MedicoS1', $row['especialidad_saliente']);
$pdf->TableCell('Medico Entrante 1', $row['nombre_funcionario_entrante_1']);
$pdf->TableCell('Especialidad Medico E1', $row['especialidad_entrante']);

$pdf->ChapterTitle('Pacientes Hospitalizados');
$pdf->TableCell('Cama Hospitalizado', $row['cama_hospitalizado']);
$pdf->TableCell('Nombre Hospitalizado', $row['nombre_hospitalizado']);
$pdf->TableCell('Edad Hospitalizado', $row['edad_hospitalizado']);
$pdf->TableCell('EIH Hospitalizado', $row['eih_hospitalizado']);
$pdf->TableCell('Diagnostico', $row['diagnosticos_hospitalizado']);
$pdf->TableCell('Novedades', $row['novedades_hospitalizado']);
$pdf->TableCell('Planes Hospitalizado', $row['planes_hospitalizado']);

$pdf->ChapterTitle('Pacientes Egresados');
$pdf->TableCell('Nombre Egresado', $row['nombre_egresado']);
$pdf->TableCell('Destino Egresado', $row['destino_egresado']);
$pdf->TableCell('Motivo de Egreso', $row['motivo_de_egreso']);

$pdf->ChapterTitle('Pacientes Fallecidos');
$pdf->TableCell('Nombre Fallecido', $row['nombre_fallecido']);
$pdf->TableCell('Edad Fallecido', $row['edad_fallecido']);
$pdf->TableCell('Hora Fallecido', $row['hora_fallecido']);
$pdf->TableCell('Diagnosticos Fallecido', $row['diagnosticos_fallecido']);
$pdf->TableCell('Servicio Fallecido', $row['servicio_fallecido']);

$pdf->ChapterTitle('Solicitudes Rechazadas');
$pdf->TableCell('Nombre Rechazadas', $row['nombre_rechazadas']);
$pdf->TableCell('RUT Rechazado', $row['rut_rechazado']);
$pdf->TableCell('Diagnostico Rechazado', $row['diagnostico_rechazado']);
$pdf->TableCell('Servicio Rechazado', $row['servicio_rechazado']);
$pdf->TableCell('Motivo Rechazo', $row['motivo_rechazo']);

// Enviar PDF al navegador
$pdf->Output('I', 'formulario_' . $id_formulario . '.pdf');

// Cerrar la conexión
$stmt->close();
$conn->close();
?>