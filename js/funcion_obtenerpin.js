// Función para validar el PIN
function validarPIN(funcionarioId, inputPinId) {
    const selectElement = document.getElementById(funcionarioId);
    const passwordInput = document.getElementById(inputPinId);

    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const correctPIN = selectedOption.getAttribute('data-pin');

    if (passwordInput.value !== correctPIN) {
        passwordInput.setCustomValidity("El PIN ingresado no es correcto.");
        return false; 
    } else {
        passwordInput.setCustomValidity(""); 
        return true; 
    }
}

document.querySelector('form').addEventListener('submit', function(event) {

    if (!validarYEnviar(event)) {
        event.preventDefault(); 
    }
});

