document.addEventListener('DOMContentLoaded', function () {
    const ctxCovo = document.getElementById('graphCovoiturages').getContext('2d');
    const ctxCredits = document.getElementById('graphCredits').getContext('2d');

    new Chart(ctxCovo, {
        type: 'bar',
        data: covoData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: 'Nombre de covoiturages par jour' }
            }
        }
    });

    new Chart(ctxCredits, {
        type: 'bar',
        data: creditsData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: 'Crédits gagnés par jour' }
            }
        }
    });
});
