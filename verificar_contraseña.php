<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password_ingresada = $_POST['password'];
    
    // Definir una contraseña fija
    $contraseña_fija = "EDZiSdUjfsZBst/tKiIONZg###";

    if ($password_ingresada === $contraseña_fija) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
?>
