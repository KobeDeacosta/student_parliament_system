// 🔎 search.js
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("finesTable");

    searchInput.addEventListener("keyup", () => {
        const filter = searchInput.value.toLowerCase();
        const rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) { // skip header
            const cells = rows[i].getElementsByTagName("td");
            let match = false;

            for (let j = 0; j < cells.length; j++) {
                const text = cells[j].textContent || cells[j].innerText;
                if (text.toLowerCase().includes(filter)) {
                    match = true;
                    break;
                }
            }

            rows[i].style.display = match ? "" : "none";
        }
    });
});
