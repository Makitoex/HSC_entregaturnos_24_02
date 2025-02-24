function validarYEnviarupcmedico(event) {
    const funcionario1 = document.getElementById('funcionario_saliente_1'); 
    const contraseña1 = document.getElementById('contrasena_saliente_1'); 

    if (!funcionario1 || !contraseña1) {
        alert('Error: No se encontraron los campos de funcionario o PIN.');
        event.preventDefault();  // Evita el envío
        return false;
    }

    if (funcionario1.selectedIndex === -1) {
        alert('Por favor, selecciona un funcionario.');
        event.preventDefault();  // Evita el envío
        return false;
    }

    // Obtener el PIN correcto
    const pinCorrecto1 = funcionario1.options[funcionario1.selectedIndex].getAttribute('data-pin').trim();

    if (contraseña1.value.trim() !== pinCorrecto1) {
        alert('El PIN del Funcionario Saliente 1 es incorrecto.');
        event.preventDefault();  // Evita el envío
        return false;
    }

    return true;
}

document.querySelector('form').addEventListener('submit', function(event) {
    if (!validarYEnviarupcmedico(event)) {
        event.preventDefault();  // Evita el envío si la validación falla
    }
});
