<?php
require 'vendor/autoload.php';
include 'conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$table = $_POST['table'] ?? '';

if ($table) {
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Obtiene nombres y columnas
        $all_columns = array_keys($result->fetch_assoc());
        $columns = array_filter($all_columns, function($col) {
            return stripos($col, 'contrasena') === false;
        });

        // Restablece puntero 
        $result->data_seek(0);

        // Escribir encabezados de columnas
        $colIndex = 'A';
        foreach ($columns as $column) {
            $sheet->setCellValue($colIndex . '1', $column);
            $colIndex++;
        }

        // Escribir filas de datos
        $rowIndex = 2;
        while ($row = $result->fetch_assoc()) {
            $colIndex = 'A';
            foreach ($columns as $column) {
                $sheet->setCellValue($colIndex . $rowIndex, $row[$column]);
                $colIndex++;
            }
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'tabla_' . $table . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    } else {
        echo "No hay datos disponibles en la tabla seleccionada.";
    }
} else {
    echo "No se ha seleccionado ninguna tabla.";
}
?>