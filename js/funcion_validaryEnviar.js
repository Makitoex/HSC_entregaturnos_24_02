function validarYEnviar(event) {
    const funcionario1 = document.getElementById('funcionario_saliente_1'); 
    const contraseña1 = document.getElementById('contrasena_saliente_1'); 
    const funcionario2 = document.getElementById('funcionario_saliente_2'); 
    const contraseña2 = document.getElementById('contrasena_saliente_2'); 
// se verifica que los funcionarios esten seleccionados
    if (!funcionario1 || !funcionario2 || !contraseña1 || !contraseña2) {
        alert('Error: No se encontraron los campos de funcionarios o PIN.');
        event.preventDefault();
        return false;
    }

    // Verificar que se haya seleccionado un funcionario
    if (funcionario1.selectedIndex === -1 || funcionario2.selectedIndex === -1) {
        alert('Por favor, selecciona ambos funcionarios.');
        event.preventDefault();  
        return false;
    }

    const pinCorrecto1 = funcionario1.options[funcionario1.selectedIndex].getAttribute('data-pin').trim();
    const pinCorrecto2 = funcionario2.options[funcionario2.selectedIndex].getAttribute('data-pin').trim();

if (contraseña1.value.trim() !== pinCorrecto1) {
    alert('El PIN del Funcionario 1 es incorrecto.');
    return false;
}

if (contraseña2.value.trim() !== pinCorrecto2) {
    alert('El PIN del Funcionario 2 es incorrecto.');
    return false;
}

    // Si ambos PINs son correctos, se envia
    return true;
}

// Agregar el evento submit para que ejecute la validación
document.querySelector('form').addEventListener('submit', function(event) {
    if (!validarYEnviar(event)) {
        event.preventDefault(); 
    }
});
