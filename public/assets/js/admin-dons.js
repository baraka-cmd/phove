/**
 * AHD-RDC — SCRIPTS GESTION DONS (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.dons-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DELAI (DEBOUNCE)
    // ============================================================
    const searchInput = document.getElementById('donSearch');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const form = this.closest('form');
                if (form) form.submit();
            }, 500);
        });
    }

    // ============================================================
    // 3. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-don');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement ce don ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 4. GRAPHIQUE BARRES — Dons par mois
    // ============================================================
    const barCanvas = document.getElementById('donsBarChart');

    if (barCanvas && typeof Chart !== 'undefined') {
        try {
            const barLabels = JSON.parse(barCanvas.getAttribute('data-labels') || '[]');
            const barData = JSON.parse(barCanvas.getAttribute('data-values') || '[]');

            new Chart(barCanvas, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Dons reçus ($)',
                        data: barData,
                        backgroundColor: barData.map((val, i) => {
                            const alpha = 0.55 + (i / barData.length) * 0.45;
                            return `rgba(22, 85, 143, ${alpha})`;
                        }),
                        borderColor: '#16558F',
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1a1a1a',
                            titleFont: { family: 'Poppins', size: 12, weight: '600' },
                            bodyFont: { family: 'Poppins', size: 11 },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (context) {
                                    return ' ' + context.parsed.y.toLocaleString('fr-FR') + ' $';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { family: 'Poppins', size: 11 }, color: '#888' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.04)', drawBorder: false },
                            ticks: {
                                font: { family: 'Poppins', size: 11 },
                                color: '#888',
                                callback: function (value) {
                                    return value.toLocaleString('fr-FR') + ' $';
                                }
                            }
                        }
                    },
                    animation: { duration: 1000, easing: 'easeOutQuart' }
                }
            });
        } catch (e) {
            console.error('Bar chart error:', e);
        }
    }

    // ============================================================
    // 5. GRAPHIQUE ANNEAU — Par mode de paiement
    // ============================================================
    const donutCanvas = document.getElementById('donsDonutChart');

    if (donutCanvas && typeof Chart !== 'undefined') {
        try {
            const donutLabels = JSON.parse(donutCanvas.getAttribute('data-labels') || '[]');
            const donutData = JSON.parse(donutCanvas.getAttribute('data-values') || '[]');
            const donutColors = JSON.parse(donutCanvas.getAttribute('data-colors') || '[]');
            const donutTotal = donutData.reduce((a, b) => a + b, 0);

            const centerVal = document.getElementById('donutCenterValue');
            if (centerVal) {
                centerVal.textContent = donutTotal.toLocaleString('fr-FR') + ' $';
            }

            new Chart(donutCanvas, {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutData,
                        backgroundColor: donutColors,
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1a1a1a',
                            titleFont: { family: 'Poppins', size: 12, weight: '600' },
                            bodyFont: { family: 'Poppins', size: 11 },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (context) {
                                    const value = context.parsed;
                                    const percentage = donutTotal > 0 ? Math.round((value / donutTotal) * 100) : 0;
                                    return ' ' + value.toLocaleString('fr-FR') + ' $ — ' + percentage + '%';
                                }
                            }
                        }
                    },
                    animation: { animateScale: true, animateRotate: true, duration: 1200 }
                }
            });
        } catch (e) {
            console.error('Donut chart error:', e);
        }
    }

    // ============================================================
    // 6. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.don-stat-card');

    if (statCards.length > 0 && 'IntersectionObserver' in window) {
        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 80);
                    statObserver.unobserve(card);
                }
            });
        }, { threshold: 0.2, rootMargin: '0px 0px -20px 0px' });

        statCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(15px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            statObserver.observe(card);
        });
    }

    // ============================================================
    // 7. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.don-stat-value');

    if (kpiValues.length > 0 && 'IntersectionObserver' in window) {
        const kpiObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const text = el.textContent.replace(/[^0-9]/g, '');
                    const target = parseInt(text) || 0;
                    if (target > 0) {
                        animateNumber(el, target);
                    }
                    kpiObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        kpiValues.forEach(el => kpiObserver.observe(el));
    }

    function animateNumber(element, target) {
        const duration = 1000;
        const startTime = performance.now();
        const startValue = 0;
        const isMoney = element.textContent.includes('$');

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(2, -8 * progress);
            const currentValue = Math.floor(startValue + (target - startValue) * eased);
            element.textContent = isMoney
                ? currentValue.toLocaleString('fr-FR') + ' $'
                : currentValue.toLocaleString('fr-FR');
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = isMoney
                    ? target.toLocaleString('fr-FR') + ' $'
                    : target.toLocaleString('fr-FR');
            }
        }
        requestAnimationFrame(update);
    }

    // ============================================================
    // 8. SURVOL LIGNES TABLEAU
    // ============================================================
    const tableRows = document.querySelectorAll('.table-dons tbody tr');

    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function () {
            this.style.transition = 'background 0.2s ease';
        });
    });

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c💰 AHD-RDC %c— Gestion Dons chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});