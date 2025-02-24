function filtrarTabla() {
    let input = document.getElementById("filtrar_tabla");
    let filter = input.value.toLowerCase();
    let table = document.querySelector("table");
    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName("td");
        let encontrado = false;

        for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerText.toLowerCase().includes(filter)) {
                encontrado = true;
                break;
            }
        }
        rows[i].style.display = encontrado ? "" : "none";
    }
}
