function filtrartablasadmin() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('dataTable');
    const trs = table.getElementsByTagName('tr');

    for (let i = 1; i < trs.length; i++) { // Empezar desde 1 para saltar el encabezado
        let display = false;
        const tds = trs[i].getElementsByTagName('td');
        for (let j = 0; j < tds.length; j++) {
            if (tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
                display = true;
                break;
            }
        }
        trs[i].style.display = display ? '' : 'none';
    }
}