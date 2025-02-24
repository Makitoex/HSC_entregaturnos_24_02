function agregarFuncionario(inputId, listaId) {

    const funcionario = document.getElementById(inputId).value;
    
    if (funcionario.trim() !== "") {
        const li = document.createElement("li");
        li.textContent = funcionario;

        document.getElementById(listaId).appendChild(li);
        
        document.getElementById(inputId).value = "";
    } else {
        alert("Por favor, ingresa un nombre.");
    }
}
