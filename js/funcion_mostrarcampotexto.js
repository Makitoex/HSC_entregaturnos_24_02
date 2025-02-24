function mostrarCampoTexto() {
    var select = document.getElementById('cantpacientesfallecidos');
    var detallespacientesf = document.getElementById('detallespacientesf');

    if (select.value > 0) {
        detallespacientesf.style.display = 'block';
    } else {
        detallespacientesf.style.display = 'none';
    }
}