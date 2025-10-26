function toggleDetails(id) {
    const elem = document.getElementById(id);
    elem.style.display = (elem.style.display === "none" ? "block" : "none");
}

// Double confirmation avant participation
function confirmerParticipation(prix) {
    if (!confirm("Confirmez-vous vouloir participer à ce covoiturage ?")) return false;
    return confirm("Ce trajet coûte " + prix + " crédits. Souhaitez-vous les utiliser ?");
}


