function validarYEnviarkineuci() {
    // Obtener los valores de los campos de contraseña y PIN de los funcionarios
    const contrasenaSaliente1 = document.getElementById('contrasena_saliente_1').value.trim(); 
    const funcionarioSaliente1 = document.getElementById('funcionario_saliente_1');
    const pinSaliente1 = funcionarioSaliente1.options[funcionarioSaliente1.selectedIndex].getAttribute('data-pin').trim();  // trim() también para el PIN

    console.log('Contraseña ingresada:', contrasenaSaliente1);
    console.log('PIN de funcionario saliente:', pinSaliente1);

    if (contrasenaSaliente1 !== pinSaliente1) {
        alert('La contraseña del Funcionario saliente 1 es incorrecta.');
        return false;  // Detener el envío del formulario si las contraseñas no coinciden
    }

    return true;  // Si las contraseñas coinciden, enviar el formulario
}
