function validarYEnviarkine(event) {
    // Obtener los elementos del funcionario saliente y su contraseña
    const funcionario1 = document.getElementById('funcionario_saliente_1'); 
    const contraseña1 = document.getElementById('contrasena_saliente_1'); 

    
    if (!funcionario1 || !contraseña1) {
        alert('Error: No se encontraron los campos de funcionario o PIN.');
        event.preventDefault();
        return false;
    }

    // Verificar que se haya seleccionado un funcionario
    if (funcionario1.selectedIndex === -1) {
        alert('Por favor, selecciona un funcionario.');
        event.preventDefault();  
        return false;
    }

    // Obtener el PIN correcto
    const pinCorrecto1 = funcionario1.options[funcionario1.selectedIndex].getAttribute('data-pin').trim();

    // Verificar que la contraseña coincida con el PIN correspondiente almacenados en la bd
    if (contraseña1.value.trim() !== pinCorrecto1) {
        alert('El PIN del Funcionario Saliente 1 es incorrecto.');
        event.preventDefault();  // Prevenir el envío del formulario
        return false;
    }

    // Si el PIN es correcto, permitir el envío del formulario
    return true;
}

// Agregar el evento submit para que ejecute la validación
document.querySelector('form').addEventListener('submit', function(event) {
    if (!validarYEnviar(event)) {
        event.preventDefault();  // Evitar el envío si la validación falla
    }
});
