function agregarFilahospitalizados() {
    var tabla = document.getElementById("tablaHospitalizados").getElementsByTagName('tbody')[0];
    var nuevaFila = tabla.rows[0].cloneNode(true);
    var inputs = nuevaFila.getElementsByTagName('input');
    var textareas = nuevaFila.getElementsByTagName('textarea');
    
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].value = "";
    }
    for (var i = 0; i < textareas.length; i++) {
        textareas[i].value = "";
    }
    tabla.appendChild(nuevaFila);
}

function agregarFilaegresados() {
    var tabla = document.getElementById("tablaegresados").getElementsByTagName('tbody')[0];
    var nuevaFila = tabla.rows[0].cloneNode(true);
    var textareas = nuevaFila.getElementsByTagName('textarea');

    for (var i = 0; i < textareas.length; i++) {
        textareas[i].value = "";
    }
    tabla.appendChild(nuevaFila);
}

function agregarFilafallecidos() {
    var tabla = document.getElementById("tablafallecidos").getElementsByTagName('tbody')[0];
    var nuevaFila = tabla.rows[0].cloneNode(true);
    var textareas = nuevaFila.getElementsByTagName('textarea');
    
    for (var i = 0; i < textareas.length; i++) {
        textareas[i].value = "";
    }
    tabla.appendChild(nuevaFila);
}

function agregarFilarechazadas() {
    var tabla = document.getElementById("tablarechazadas").getElementsByTagName('tbody')[0];
    var nuevaFila = tabla.rows[0].cloneNode(true);
    var textareas = nuevaFila.getElementsByTagName('textarea');
    
    for (var i = 0; i < textareas.length; i++) {
        textareas[i].value = "";
    }
    tabla.appendChild(nuevaFila);
}


function eliminarFila(button) {
    var row = button.closest("tr");
  
    row.remove();
}
