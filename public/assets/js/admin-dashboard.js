/**
 * AHD-RDC — SCRIPTS DASHBOARD ADMINISTRATEUR (COMPLET)
 * Action Humanitaire pour le Développement en RD Congo
 * Graphiques Chart.js — Donut, Barres, Ligne, Horizontal
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. HORLOGE DASHBOARD
    // ============================================================
    function updateDashboardClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const clockEl = document.getElementById('dashboardClock');
        if (clockEl) {
            clockEl.textContent = hours + ':' + minutes;
        }
    }
    updateDashboardClock();
    setInterval(updateDashboardClock, 30000);

    // ============================================================
    // 2. COULEURS DES DOMAINES (identiques pour tous les graphiques)
    // ============================================================
    const domainColors = {
        'Santé-Nutrition': '#16558F',
        'Éducation': '#28a745',
        'Protection': '#8e44ad',
        'Eau & Assainissement': '#2980b9',
        'Sécurité Alimentaire': '#e67e22',
        'Communication': '#17a2b8',
        'Environnement': '#27ae60',
        'Paix & Cohésion': '#D32F2F'
    };

    const domainColorsArray = Object.values(domainColors);
    const domainLabels = Object.keys(domainColors);

    // ============================================================
    // 3. GRAPHIQUE EN ANNEAU — Répartition des projets par domaine
    // ============================================================
    const donutCanvas = document.getElementById('donutChart');

    if (donutCanvas) {
        const donutData = JSON.parse(donutCanvas.getAttribute('data-values') || '[]');
        const donutLabels = JSON.parse(donutCanvas.getAttribute('data-labels') || '[]');
        const donutTotal = donutData.reduce((a, b) => a + b, 0);

        // Afficher le total au centre
        const centerValue = document.getElementById('donutCenterValue');
        if (centerValue) {
            centerValue.textContent = donutTotal;
        }

        const donutCtx = donutCanvas.getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: donutLabels.map(label => domainColors[label] || '#ccc'),
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#ffffff'
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
                        titleFont: { family: 'Poppins', size: 13, weight: '600' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 14,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 4,
                        callbacks: {
                            label: function (context) {
                                const value = context.parsed;
                                const percentage = donutTotal > 0 ? Math.round((value / donutTotal) * 100) : 0;
                                return ' ' + value + ' projet(s) — ' + percentage + '%';
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1200
                }
            }
        });
    }

    // ============================================================
    // 4. GRAPHIQUE EN BARRES VERTICALES — Dons par mois
    // ============================================================
    const barCanvas = document.getElementById('barChart');

    if (barCanvas) {
        const barData = JSON.parse(barCanvas.getAttribute('data-values') || '[]');
        const barLabels = JSON.parse(barCanvas.getAttribute('data-labels') || '[]');

        const barCtx = barCanvas.getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Dons reçus ($)',
                    data: barData,
                    backgroundColor: barData.map((val, i) => {
                        const alpha = 0.6 + (i / barData.length) * 0.4;
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
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#888'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#888',
                            callback: function (value) {
                                return value.toLocaleString('fr-FR') + ' $';
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // ============================================================
    // 5. GRAPHIQUE LINÉAIRE — Évolution des membres
    // ============================================================
    const lineCanvas = document.getElementById('lineChart');

    if (lineCanvas) {
        const lineData = JSON.parse(lineCanvas.getAttribute('data-values') || '[]');
        const lineLabels = JSON.parse(lineCanvas.getAttribute('data-labels') || '[]');

        const lineCtx = lineCanvas.getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: 'Nouveaux membres',
                    data: lineData,
                    borderColor: '#D32F2F',
                    backgroundColor: 'rgba(211, 47, 47, 0.06)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#D32F2F',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#D32F2F',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
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
                                return ' ' + context.parsed.y + ' membre(s)';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#888'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#888',
                            stepSize: 1
                        }
                    }
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // ============================================================
    // 6. GRAPHIQUE BARRES HORIZONTALES — Top projets par budget
    // ============================================================
    const horizontalBarCanvas = document.getElementById('horizontalBarChart');

    if (horizontalBarCanvas) {
        const hBarData = JSON.parse(horizontalBarCanvas.getAttribute('data-values') || '[]');
        const hBarLabels = JSON.parse(horizontalBarCanvas.getAttribute('data-labels') || '[]');
        const hBarMax = Math.max(...hBarData, 1);

        const hBarCtx = horizontalBarCanvas.getContext('2d');
        new Chart(hBarCtx, {
            type: 'bar',
            data: {
                labels: hBarLabels,
                datasets: [{
                    data: hBarData,
                    backgroundColor: hBarData.map((val, i) => {
                        const colors = ['#16558F', '#28a745', '#e67e22', '#8e44ad', '#D32F2F'];
                        return colors[i] || '#16558F';
                    }),
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 28
                }]
            },
            options: {
                indexAxis: 'y',
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
                                return ' ' + context.parsed.x.toLocaleString('fr-FR') + ' $';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: hBarMax * 1.15,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false
                        },
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
                                return label.length > 35 ? label.substring(0, 35) + '...' : label;
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // ============================================================
// ANIMATION DES BARRES DE DOMAINE
// ============================================================
const domainBars = document.querySelectorAll('.domain-bar-fill');

if (domainBars.length > 0 && 'IntersectionObserver' in window) {
    const barObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const targetWidth = bar.getAttribute('style')?.match(/width:\s*(\d+)%/)?.[1];
                if (targetWidth) {
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = targetWidth + '%';
                    }, 200);
                }
                barObserver.unobserve(bar);
            }
        });
    }, { threshold: 0.3 });

    domainBars.forEach(bar => barObserver.observe(bar));
}

    // ============================================================
    // 8. RAFRAÎCHIR LE DASHBOARD (toutes les 5 minutes)
    // ============================================================
    let refreshInterval = 300000; // 5 minutes

    function refreshDashboard() {
        // Afficher un loader discret
        const loader = document.getElementById('globalLoader');
        if (loader) loader.classList.add('active');

        // Recharger la page
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    // Décommente pour activer le rafraîchissement auto :
    // setInterval(refreshDashboard, refreshInterval);

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c📊 AHD-RDC %c— Dashboard Administrateur chargé avec graphiques',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%cChart.js initialisé — Donut, Barres, Ligne, Horizontal',
        'color: #28a745; font-size: 11px;'
    );

});