function mostrarOpcionesDomingo() {
    const fecha = new Date(document.getElementById('fecha').value);
    const diaSemana = fecha.getUTCDay(); // 0 es domingo, 6 es sábado

    // Muestra u oculta las opciones dependiendo si es domingo (0)
    if (diaSemana === 0) {
        document.getElementById('opciones_domingo').style.display = 'block';
    } else {
        document.getElementById('opciones_domingo').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
  
    mostrarOpcionesDomingo();

    
    document.getElementById('fecha').addEventListener('change', mostrarOpcionesDomingo);
});
