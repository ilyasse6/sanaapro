document.addEventListener("DOMContentLoaded", function () {
    const table = $("#modulesTableList").DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            search: "Rechercher :",
            lengthMenu: "Afficher _MENU_ entrées",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            infoEmpty: "Aucune entrée trouvée",
            infoFiltered: "(filtré de _MAX_ entrées au total)",
            zeroRecords: "Aucun résultat trouvé",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent",
            },
        },
    });

    // Toggle form visibility
    $("#toggleFormBtn").click(function () {
        $("#form-container-module").fadeToggle();
    });

    // Search functionality
    $("#searchNom").on("keyup", function () {
        table.columns(1).search(this.value).draw();
    });

    $("#searchStatut").on("change", function () {
        table.columns(2).search(this.value).draw();
    });

    $("#searchDateDebut, #searchDateFin").on("change", function () {
        const dateDebut = $("#searchDateDebut").val();
        const dateFin = $("#searchDateFin").val();

        if (dateDebut || dateFin) {
            table
                .columns(3)
                .search(dateDebut + "|" + dateFin, true, false)
                .draw();
        } else {
            table.columns(3).search("").draw();
        }
    });

    // Clear filters
    $("#clearFiltersBtn").click(function () {
        $("#searchNom").val("");
        $("#searchStatut").val("");
        $("#searchDateDebut").val("");
        $("#searchDateFin").val("");
        table.search("").columns().search("").draw();
    });
});
