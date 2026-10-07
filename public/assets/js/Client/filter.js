document.addEventListener("DOMContentLoaded", function () {
    const table = $("#clientsTableList").DataTable({
        responsive: true,
        autoWidth: false,
        drawCallback: function () {
            if (window.ProwiseTableActionDropdowns && typeof window.ProwiseTableActionDropdowns.init === "function") {
                window.ProwiseTableActionDropdowns.init();
            }
        },
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
        $("#form-container-client").fadeToggle();
    });

    // Search: colonnes 1=Raison sociale, 2=Contact, 3=Email, 4=Téléphone, 5=Ville, 6=Secteur
    $("#searchNom").on("keyup", function () {
        table.columns(1).search(this.value).draw();
    });

    $("#searchPrenom").on("keyup", function () {
        table.columns(2).search(this.value).draw();
    });

    $("#searchEmail").on("keyup", function () {
        table.columns(3).search(this.value).draw();
    });

    $("#searchTelephone").on("keyup", function () {
        table.columns(4).search(this.value).draw();
    });

    // Clear filters
    $("#clearFiltersBtn").click(function () {
        $("#searchNom").val("");
        $("#searchPrenom").val("");
        $("#searchEmail").val("");
        $("#searchTelephone").val("");
        table.search("").columns().search("").draw();
    });
});

