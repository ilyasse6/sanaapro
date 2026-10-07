document.addEventListener("DOMContentLoaded", function () {
    const table = $("#specialistesTableList").DataTable({
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

    // Variable pour stocker les données originales
    let originalData = [];
    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
        let data = this.data();
        originalData.push({
            row: this,
            intitule: data[1],
            description: data[2],
            date: data[3],
            statut: data[4],
        });
    });

    // Fonction de filtrage par intitulé
    $("#searchIntitule").on("keyup", function () {
        const searchValue = this.value.toLowerCase();
        table.column(1).search(searchValue).draw();
    });

    // Fonction de filtrage par statut
    $("#searchStatut").on("change", function () {
        const statutValue = this.value;
        if (statutValue === "") {
            table.column(4).search("").draw();
        } else {
            table.column(4).search(statutValue).draw();
        }
    });

    // Fonction de filtrage par plage de dates
    function filterByDateRange() {
        const dateDebut = $("#searchDateDebut").val();
        const dateFin = $("#searchDateFin").val();

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            const dateCell = data[3]; // Colonne des dates (index 3)

            if (!dateDebut && !dateFin) {
                return true;
            }

            // Convertir la date de la table au format YYYY-MM-DD
            const dateMatch = dateCell.match(/(\d{4})-(\d{2})-(\d{2})/);
            if (!dateMatch) {
                return false;
            }

            const tableDate = dateMatch[0]; // Format YYYY-MM-DD

            // Vérifier si la date est dans la plage
            if (dateDebut && tableDate < dateDebut) {
                return false;
            }
            if (dateFin && tableDate > dateFin) {
                return false;
            }

            return true;
        });

        table.draw();

        // Nettoyer le filtre personnalisé après utilisation
        $.fn.dataTable.ext.search.pop();
    }

    // Événements pour les champs de date
    $("#searchDateDebut, #searchDateFin").on("change", function () {
        filterByDateRange();
    });

    // Réinitialisation des filtres
    $("#clearFiltersBtn").on("click", function () {
        // Réinitialiser les valeurs des champs
        $("#searchIntitule").val("");
        $("#searchStatut").val("");
        $("#searchDateDebut").val("");
        $("#searchDateFin").val("");

        // Nettoyer tous les filtres et redessiner le tableau
        table.search("").columns().search("").draw();

        // S'assurer que les filtres personnalisés sont également nettoyés
        $.fn.dataTable.ext.search = [];
    });

    // Toggle du formulaire de recherche avec animation
    $("#toggleFormBtn").on("click", function () {
        const formContainer = $("#form-container-exception");
        const button = $(this);

        if (formContainer.is(":visible")) {
            formContainer.slideUp(300);
        } else {
            formContainer.slideDown(300);
        }
    });

    // Amélioration de l'UX : feedback visuel lors du filtrage
    $("#searchIntitule, #searchStatut, #searchDateDebut, #searchDateFin").on(
        "input change",
        function () {
            const activeFilters = [];

            if ($("#searchIntitule").val()) activeFilters.push("Intitulé");
            if ($("#searchStatut").val()) activeFilters.push("Statut");
            if ($("#searchDateDebut").val() || $("#searchDateFin").val())
                activeFilters.push("Date");

            const toggleBtn = $("#toggleFormBtn");
            if (activeFilters.length > 0) {
                toggleBtn.removeClass("btn-dark").addClass("btn-warning");
                toggleBtn
                    .find("i")
                    .removeClass("bi-funnel")
                    .addClass("bi-funnel-fill");
            } else {
                toggleBtn.removeClass("btn-warning").addClass("btn-dark");
                toggleBtn
                    .find("i")
                    .removeClass("bi-funnel-fill")
                    .addClass("bi-funnel");
            }
        }
    );
});
