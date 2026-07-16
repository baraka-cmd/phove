/**
 * AHD-RDC — SCRIPTS GESTION BUDGETS (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.budgets-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DELAI (DEBOUNCE)
    // ============================================================
    const searchInput = document.getElementById('budgetSearch');
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
    // 3. MISE À JOUR RAPIDE DU BUDGET (INPUT INLINE)
    // ============================================================
    const updateForms = document.querySelectorAll('.budget-update-form');

    updateForms.forEach(form => {
        const input = form.querySelector('.budget-update-input');
        const btn = form.querySelector('.budget-update-btn');

        if (input && btn) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (confirm('Mettre à jour le budget utilisé de ce projet ?')) {
                        form.submit();
                    }
                }
            });

            btn.addEventListener('click', function (e) {
                if (!confirm('Mettre à jour le budget utilisé de ce projet ?')) {
                    e.preventDefault();
                }
            });
        }
    });

    // ============================================================
    // 4. GRAPHIQUE BARRES — Budget total vs Utilisé (Top 10)
    // ============================================================
    const barCanvas = document.getElementById('budgetsBarChart');

    if (barCanvas && typeof Chart !== 'undefined') {
        try {
            const barLabels = JSON.parse(barCanvas.getAttribute('data-labels') || '[]');
            const barTotal = JSON.parse(barCanvas.getAttribute('data-total') || '[]');
            const barUtilise = JSON.parse(barCanvas.getAttribute('data-utilise') || '[]');

            new Chart(barCanvas, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [
                        {
                            label: 'Budget total',
                            data: barTotal,
                            backgroundColor: 'rgba(22, 85, 143, 0.3)',
                            borderColor: '#16558F',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        },
                        {
                            label: 'Budget utilisé',
                            data: barUtilise,
                            backgroundColor: 'rgba(211, 47, 47, 0.6)',
                            borderColor: '#D32F2F',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
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
                                    return ' ' + context.dataset.label + ' : ' + context.parsed.x.toLocaleString('fr-FR') + ' $';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.04)', drawBorder: false },
                            ticks: {
                                font: { family: 'Poppins', size: 10 },
                                color: '#888',
                                callback: function (value) {
                                    return value.toLocaleString('fr-FR') + ' $';
                                }
                            }
                        },
                        y: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                font: { family: 'Poppins', size: 11 },
                                color: '#555',
                                callback: function (value) {
                                    const label = this.getLabelForValue(value);
                                    return label.length > 30 ? label.substring(0, 30) + '...' : label;
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
    // 5. GRAPHIQUE ANNEAU — Dépenses par domaine
    // ============================================================
    const donutCanvas = document.getElementById('budgetsDonutChart');

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
    // 6. ANIMATION DES BARRES DE PROGRESSION
    // ============================================================
    const progressBars = document.querySelectorAll('.budget-progress-fill');

    if (progressBars.length > 0 && 'IntersectionObserver' in window) {
        const barObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const targetWidth = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 200);
                    barObserver.unobserve(bar);
                }
            });
        }, { threshold: 0.5 });

        progressBars.forEach(bar => barObserver.observe(bar));
    }

    // ============================================================
    // 7. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.budget-stat-card');

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
    // 8. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.budget-stat-value');

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
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c💰 AHD-RDC %c— Gestion Budgets chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});