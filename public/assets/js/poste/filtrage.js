document.addEventListener("DOMContentLoaded", function () {
    const table = $("#postesTableList").DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            search: "Rechercher :",
            lengthMenu: "Afficher *MENU* entrées",
            info: "Affichage de *START* à *END* sur *TOTAL* entrées",
            infoEmpty: "Aucune entrée trouvée",
            infoFiltered: "(filtré de *MAX* entrées au total)",
            zeroRecords: "Aucun résultat trouvé",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent",
            },
        },
        columnDefs: [
            {
                targets: [0, 5],
                searchable: false,
            }, // Désactiver la recherche pour # et Action seulement
        ],
    });

    // Fonction de filtrage personnalisée
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        // Récupérer les valeurs des filtres
        var searchIntitule = $("#searchIntitule").val().toLowerCase();
        var searchStatut = $("#searchStatut").val();
        var searchDateDebut = $("#searchDateDebut").val();
        var searchDateFin = $("#searchDateFin").val();

        // Récupérer les données de la ligne
        var intitule = data[1].toLowerCase(); // Colonne Intitulé
        var statutCell = data[3]; // Colonne Statut (contient le HTML du badge)
        var dateAjout = data[4]; // Colonne Date d'ajout (format YYYY-MM-DD)

        // Extraire le texte du statut depuis le HTML du badge
        var statutText = $(statutCell).text().trim(); // Utilise jQuery pour extraire le texte

        // Filtrer par intitulé
        if (searchIntitule && !intitule.includes(searchIntitule)) {
            return false;
        }

        // Filtrer par statut
        if (searchStatut && searchStatut !== "") {
            if (statutText !== searchStatut) {
                return false;
            }
        }

        // Filtrer par date
        if (searchDateDebut || searchDateFin) {
            var datePoste = new Date(dateAjout);
            datePoste.setHours(0, 0, 0, 0); // Normaliser l'heure à minuit

            if (searchDateDebut) {
                var dateDebut = new Date(searchDateDebut);
                dateDebut.setHours(0, 0, 0, 0);
                if (datePoste < dateDebut) {
                    return false;
                }
            }

            if (searchDateFin) {
                var dateFin = new Date(searchDateFin);
                dateFin.setHours(23, 59, 59, 999); // Fin de journée
                if (datePoste > dateFin) {
                    return false;
                }
            }
        }

        return true;
    });

    // Événements pour déclencher le filtrage
    $("#searchIntitule, #searchStatut, #searchDateDebut, #searchDateFin").on(
        "keyup change",
        function () {
            table.draw();
        }
    );

    // Réinitialiser les filtres
    $("#clearFiltersBtn").click(function () {
        $("#searchIntitule").val("");
        $("#searchStatut").val("");
        $("#searchDateDebut").val("");
        $("#searchDateFin").val("");
        table.draw();
    });

    // Toggle du formulaire de filtrage
    $("#toggleFormBtn").click(function () {
        $("#form-container-exception").slideToggle();
    });
});
