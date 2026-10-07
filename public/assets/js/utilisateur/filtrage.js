function filterTableUtilisateur() {
    const nom = document.getElementById('searchNom').value.toLowerCase();
    const prenom = document.getElementById('searchPrenom').value.toLowerCase();
    const etablissement = document.getElementById('searchEtablissement').value.toLowerCase();
    const poste = document.getElementById('searchPoste').value.toLowerCase();
    const profile = document.getElementById('searchProfile').value.toLowerCase();

    document.querySelectorAll('#utilisateursTableList tbody tr').forEach(row => {
        // Utiliser les attributs data au lieu du texte affiché
        const rowNom = row.getAttribute('data-nom').toLowerCase();
        const rowPrenom = row.getAttribute('data-prenom').toLowerCase();
        const rowEtablissement = row.getAttribute('data-etablissement').toLowerCase();
        const rowPoste = row.getAttribute('data-poste').toLowerCase();
        const rowProfile = row.getAttribute('data-profile').toLowerCase();

        const matchNom = rowNom.includes(nom);
        const matchPrenom = rowPrenom.includes(prenom);
        const matchEtablissement = etablissement === '' || rowEtablissement.includes(etablissement);
        const matchPoste = poste === '' || rowPoste.includes(poste);
        const matchProfile = profile === '' || rowProfile.includes(profile);

        row.style.display = (matchNom && matchPrenom && matchEtablissement && matchPoste && matchProfile) ?
            '' : 'none';
    });
}

function clearFiltersUtilisateurs() {
    document.getElementById('searchNom').value = '';
    document.getElementById('searchPrenom').value = '';
    document.getElementById('searchEtablissement').value = '';
    document.getElementById('searchPoste').value = '';
    document.getElementById('searchProfile').value = '';
    filterTableUtilisateur();
}
function toggleSearchForm() {
    const searchForm = document.getElementById('form-container-utilisateur');
    const toggleButton = document.getElementById('toggleFormBtn');

    if (searchForm.style.display === 'none' || searchForm.style.display === '') {
        searchForm.style.display = 'block';
        // toggleButton.textContent = 'Masquer le formulaire de recherche';
    } else {
        searchForm.style.display = 'none';
        // toggleButton.textContent = 'Afficher le formulaire de recherche';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    filterTableUtilisateur();
    document.getElementById('searchNom').addEventListener('input', filterTableUtilisateur);
    document.getElementById('searchPrenom').addEventListener('input', filterTableUtilisateur);
    document.getElementById('searchEtablissement').addEventListener('change', filterTableUtilisateur);
    document.getElementById('searchPoste').addEventListener('change', filterTableUtilisateur);
    document.getElementById('searchProfile').addEventListener('change', filterTableUtilisateur);
    document.getElementById('clearFiltersBtn').addEventListener('click', clearFiltersUtilisateurs);
    document.getElementById('toggleFormBtn').addEventListener('click', toggleSearchForm);
});
