function validarYEnviarkineuci() {
    // Obtener los valores de los campos de contraseña y PIN de los funcionarios
    const contrasenaSaliente1 = document.getElementById('contrasena_saliente_1').value;
    const funcionarioSaliente1 = document.getElementById('funcionario_saliente_1');
    const pinSaliente1 = funcionarioSaliente1.options[funcionarioSaliente1.selectedIndex].getAttribute('data-pin');

    if (contrasenaSaliente1 !== pinSaliente1) {
        alert('La contraseña del Funcionario saliente 1 es incorrecta.');
        return false;
    }
    return true;
}
