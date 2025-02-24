<?php
//DATOS DE CONEXION
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "sistema_entrega_turnos_hsc"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// SI LA CONECCION ES INICIADA
if ($conn->connect_error) {
    die("ERROR: " . $conn->connect_error);
} else{ // FALTA MENSAJE 
}

?>