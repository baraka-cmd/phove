/**
 * AHD-RDC — SCRIPTS RAPPORTS FINANCIERS (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. GRAPHIQUE BARRES EMPILÉES — Entrées par mois (Dons + Cotisations)
    // ============================================================
    const stackedBarCanvas = document.getElementById('financesStackedBarChart');

    if (stackedBarCanvas && typeof Chart !== 'undefined') {
        try {
            const barLabels = JSON.parse(stackedBarCanvas.getAttribute('data-labels') || '[]');
            const donsData = JSON.parse(stackedBarCanvas.getAttribute('data-dons') || '[]');
            const cotisationsData = JSON.parse(stackedBarCanvas.getAttribute('data-cotisations') || '[]');

            new Chart(stackedBarCanvas, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [
                        {
                            label: 'Dons',
                            data: donsData,
                            backgroundColor: 'rgba(40, 167, 69, 0.75)',
                            borderColor: '#28a745',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        },
                        {
                            label: 'Cotisations',
                            data: cotisationsData,
                            backgroundColor: 'rgba(22, 85, 143, 0.75)',
                            borderColor: '#16558F',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'Poppins', size: 11 },
                                padding: 15,
                                usePointStyle: true,
                                pointStyleWidth: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1a1a1a',
                            titleFont: { family: 'Poppins', size: 12, weight: '600' },
                            bodyFont: { family: 'Poppins', size: 11 },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (context) {
                                    return ' ' + context.dataset.label + ' : ' + context.parsed.y.toLocaleString('fr-FR') + ' $';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { family: 'Poppins', size: 11 }, color: '#888' }
                        },
                        y: {
                            stacked: true,
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
            console.error('Stacked bar chart error:', e);
        }
    }

    // ============================================================
    // 2. GRAPHIQUE ANNEAU — Répartition des entrées
    // ============================================================
    const donutCanvas = document.getElementById('financesDonutChart');

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
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'Poppins', size: 10 },
                                padding: 12,
                                usePointStyle: true,
                                pointStyleWidth: 6
                            }
                        },
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
    // 3. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.finance-stat-card');

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
    // 4. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.finance-stat-value');

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
    // 5. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-rapport');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce rapport financier ?')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 6. INITIALISATION
    // ============================================================
    console.log(
        '%c💰 AHD-RDC %c— Rapports Financiers chargés',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});