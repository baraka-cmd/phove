/**
 * AHD-RDC — SCRIPTS GESTION INDICATEURS (KPI)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. GRAPHIQUE EN BARRES — Progression par indicateur
    // ============================================================
    const barCanvas = document.getElementById('indicateursBarChart');

    if (barCanvas && typeof Chart !== 'undefined') {
        try {
            const barLabels = JSON.parse(barCanvas.getAttribute('data-labels') || '[]');
            const barData = JSON.parse(barCanvas.getAttribute('data-values') || '[]');
            const barColors = JSON.parse(barCanvas.getAttribute('data-colors') || '[]');

            new Chart(barCanvas, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Progression (%)',
                        data: barData,
                        backgroundColor: barColors,
                        borderColor: barColors,
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
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
                                    return ' ' + context.parsed.x + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.04)', drawBorder: false },
                            ticks: {
                                font: { family: 'Poppins', size: 11 },
                                color: '#888',
                                callback: function (value) { return value + '%'; }
                            }
                        },
                        y: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                font: { family: 'Poppins', size: 11 },
                                color: '#555',
                                callback: function (value) {
                                    const label = this.getLabelForValue(value);
                                    return label.length > 35 ? label.substring(0, 35) + '...' : label;
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
    // 2. GRAPHIQUE EN ANNEAU — Atteints vs En cours
    // ============================================================
    const donutCanvas = document.getElementById('indicateursDonutChart');

    if (donutCanvas && typeof Chart !== 'undefined') {
        try {
            const donutLabels = JSON.parse(donutCanvas.getAttribute('data-labels') || '[]');
            const donutData = JSON.parse(donutCanvas.getAttribute('data-values') || '[]');
            const donutColors = JSON.parse(donutCanvas.getAttribute('data-colors') || '[]');
            const donutTotal = donutData.reduce((a, b) => a + b, 0);

            const centerVal = document.getElementById('donutCenterValue');
            if (centerVal) {
                centerVal.textContent = donutTotal;
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
                    cutout: '70%',
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
                                    return ' ' + value + ' — ' + percentage + '%';
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
    // 3. ANIMATION DES BARRES DE PROGRESSION
    // ============================================================
    const progressBars = document.querySelectorAll('.indicateur-item-fill');

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
    // 4. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.indicateur-stat-card');

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
    // 5. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.indicateur-stat-value');

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

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(2, -8 * progress);
            const currentValue = Math.floor(startValue + (target - startValue) * eased);
            element.textContent = currentValue.toLocaleString('fr-FR');
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = target.toLocaleString('fr-FR');
            }
        }
        requestAnimationFrame(update);
    }

    // ============================================================
    // 6. MISE À JOUR RAPIDE DE LA VALEUR (INLINE)
    // ============================================================
    const updateForms = document.querySelectorAll('.indicateur-update-form');

    updateForms.forEach(form => {
        const input = form.querySelector('.indicateur-update-input');
        const btn = form.querySelector('.indicateur-update-btn');

        if (input && btn) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        }
    });

    // ============================================================
    // 7. INITIALISATION
    // ============================================================
    console.log(
        '%c📊 AHD-RDC %c— Gestion Indicateurs chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});