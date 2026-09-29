/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * VUE    : TABLEAU DE BORD
 * FICHIER: admin-dashboard.js
 *
 * Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies
 *
 * Fonctionnalités :
 * - Horloge temps réel
 * - Graphique en anneau (projets par domaine)
 * - Graphique en barres verticales (dons par mois)
 * - Graphique linéaire (évolution des membres)
 * - Graphique en barres horizontales (top projets)
 * - Animation des barres de domaine
 * - Compatible Chart.js v4
 * ============================================================
 */

(function () {
    'use strict';

    /* ============================================================
       1. CONFIGURATION GLOBALE
       ============================================================ */

    const CONFIG = {
        colors: {
            primary: '#1A5B9C',
            primaryDark: '#124270',
            primaryLight: '#3A7BC8',
            success: '#10B981',
            successDark: '#059669',
            warning: '#F59E0B',
            danger: '#EF4444',
            info: '#3B82F6',
            text: '#1E293B',
            muted: '#64748B',
            border: '#E2E8F0',
        },
        font: 'Poppins, sans-serif',
        animation: {
            duration: 1000,
            easing: 'easeOutQuart',
        },
    };

    /**
     * Palette des 9 domaines PHOVE.
     * Les couleurs correspondent aux vues dashboard.blade.php.
     */
    const DOMAIN_COLORS = {
        'Santé & Nutrition': '#DC2626',
        'Éducation & Formation': '#2563EB',
        'Protection Sociale': '#7C3AED',
        'Agriculture & Élevage': '#059669',
        'Infrastructures': '#EA580C',
        'Socio-Économique': '#0891B2',
        'Spirituel': '#4F46E5',
        'Psychosocial': '#DB2777',
        'Judiciaire': '#9333EA',
    };


    /* ============================================================
       2. POINT D'ENTRÉE
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function () {
        initClock();
        initDonutChart();
        initBarChart();
        initLineChart();
        initHorizontalBarChart();
        initDomainBarAnimation();
        logBanner();
    });


    /* ============================================================
       3. HORLOGE DU TABLEAU DE BORD
       ============================================================ */

    function initClock() {
        const clockEl = document.getElementById('dashboardClock');
        if (!clockEl) return;

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            clockEl.textContent = hours + ':' + minutes;
        }

        updateClock();
        setInterval(updateClock, 30000);
    }


    /* ============================================================
       4. GRAPHIQUE EN ANNEAU — PROJETS PAR DOMAINE
       ============================================================ */

    function initDonutChart() {
        const canvas = document.getElementById('donutChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = safeParse(canvas.dataset.labels);
        const values = safeParse(canvas.dataset.values);
        const colors = safeParse(canvas.dataset.colors);

        if (labels.length === 0) {
            renderEmptyState(canvas, 'Aucun projet');
            return;
        }

        // Utiliser les couleurs du Blade OU la palette par défaut
        const backgroundColor = labels.map((label, index) => {
            return colors[index] || DOMAIN_COLORS[label] || '#64748B';
        });

        const total = values.reduce((a, b) => a + Number(b), 0);

        // Animation du compteur central
        const centerValue = document.getElementById('donutCenterValue');
        if (centerValue) {
            animateNumber(centerValue, 0, total, 900);
        }

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: backgroundColor,
                    borderColor: '#FFFFFF',
                    borderWidth: 3,
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#FFFFFF',
                    hoverOffset: 14,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '68%',
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1200,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: CONFIG.colors.text,
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        titleFont: { family: CONFIG.font, size: 13, weight: '600' },
                        bodyFont: { family: CONFIG.font, size: 12 },
                        padding: 14,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 4,
                        callbacks: {
                            label: function (context) {
                                const value = context.parsed || 0;
                                const percentage = total > 0
                                    ? ((value / total) * 100).toFixed(1)
                                    : 0;
                                return ' ' + value + ' projet(s) — ' + percentage + '%';
                            },
                        },
                    },
                },
            },
        });
    }


    /* ============================================================
       5. GRAPHIQUE EN BARRES VERTICALES — DONS PAR MOIS
       ============================================================ */

    function initBarChart() {
        const canvas = document.getElementById('barChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = safeParse(canvas.dataset.labels);
        const values = safeParse(canvas.dataset.values);

        if (labels.length === 0) return;

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Dons reçus ($)',
                    data: values,
                    backgroundColor: values.map((val, i) => {
                        const alpha = 0.6 + (i / Math.max(values.length, 1)) * 0.4;
                        return `rgba(26, 91, 156, ${alpha})`;
                    }),
                    borderColor: CONFIG.colors.primary,
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 50,
                    hoverBackgroundColor: CONFIG.colors.primaryDark,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: CONFIG.colors.text,
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        titleFont: { family: CONFIG.font, size: 12, weight: '600' },
                        bodyFont: { family: CONFIG.font, size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return ' ' + formatMoney(context.parsed.y) + ' $';
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: CONFIG.font, size: 11 },
                            color: CONFIG.colors.muted,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: CONFIG.font, size: 11 },
                            color: CONFIG.colors.muted,
                            callback: function (value) {
                                return formatMoney(value) + ' $';
                            },
                        },
                    },
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart',
                },
            },
        });
    }


    /* ============================================================
       6. GRAPHIQUE LINÉAIRE — ÉVOLUTION DES MEMBRES
       ============================================================ */

    function initLineChart() {
        const canvas = document.getElementById('lineChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = safeParse(canvas.dataset.labels);
        const values = safeParse(canvas.dataset.values);

        if (labels.length === 0) return;

        // Création du gradient pour la zone sous la courbe
        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 320);
        gradient.addColorStop(0, 'rgba(26, 91, 156, 0.20)');
        gradient.addColorStop(1, 'rgba(26, 91, 156, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nouveaux membres',
                    data: values,
                    borderColor: CONFIG.colors.primary,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: CONFIG.colors.primary,
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: CONFIG.colors.primaryDark,
                    pointHoverBorderColor: '#FFFFFF',
                    pointHoverBorderWidth: 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: CONFIG.colors.text,
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        titleFont: { family: CONFIG.font, size: 12, weight: '600' },
                        bodyFont: { family: CONFIG.font, size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.parsed.y + ' membre(s)';
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: CONFIG.font, size: 11 },
                            color: CONFIG.colors.muted,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: CONFIG.font, size: 11 },
                            color: CONFIG.colors.muted,
                            stepSize: 1,
                        },
                    },
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart',
                },
            },
        });
    }


    /* ============================================================
       7. GRAPHIQUE EN BARRES HORIZONTALES — TOP PROJETS
       ============================================================ */

    function initHorizontalBarChart() {
        const canvas = document.getElementById('horizontalBarChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = safeParse(canvas.dataset.labels);
        const values = safeParse(canvas.dataset.values);

        if (labels.length === 0) return;

        const maxValue = Math.max(...values, 1);

        // Palette de couleurs pour les 5 projets
        const barColors = [
            CONFIG.colors.primary,
            CONFIG.colors.success,
            CONFIG.colors.warning,
            '#7C3AED',
            CONFIG.colors.danger,
        ];

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: values.map((val, i) => {
                        return barColors[i] || CONFIG.colors.primary;
                    }),
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 28,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: CONFIG.colors.text,
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        titleFont: { family: CONFIG.font, size: 12, weight: '600' },
                        bodyFont: { family: CONFIG.font, size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return ' ' + formatMoney(context.parsed.x) + ' $';
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: maxValue * 1.15,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: CONFIG.font, size: 10 },
                            color: CONFIG.colors.muted,
                            callback: function (value) {
                                return formatMoney(value) + ' $';
                            },
                        },
                    },
                    y: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: CONFIG.font, size: 11 },
                            color: CONFIG.colors.text,
                            callback: function (value) {
                                const label = this.getLabelForValue(value);
                                return label.length > 35
                                    ? label.substring(0, 35) + '...'
                                    : label;
                            },
                        },
                    },
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart',
                },
            },
        });
    }


    /* ============================================================
       8. ANIMATION DES BARRES DE DOMAINE
       ============================================================ */

    function initDomainBarAnimation() {
        const bars = document.querySelectorAll('.domain-bar-fill');

        if (bars.length === 0 || !('IntersectionObserver' in window)) {
            return;
        }

        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;

                    const bar = entry.target;
                    const inlineStyle = bar.getAttribute('style') || '';
                    const match = inlineStyle.match(/width:\s*([\d.]+)%/);
                    const targetWidth = match ? match[1] : null;

                    if (targetWidth) {
                        bar.style.width = '0%';
                        requestAnimationFrame(function () {
                            setTimeout(function () {
                                bar.style.width = targetWidth + '%';
                                bar.style.transition = 'width 0.9s cubic-bezier(0.4, 0, 0.2, 1)';
                            }, 150);
                        });
                    }

                    observer.unobserve(bar);
                });
            },
            { threshold: 0.3 }
        );

        bars.forEach(function (bar) {
            observer.observe(bar);
        });
    }


    /* ============================================================
       9. FONCTIONS UTILITAIRES
       ============================================================ */

    /**
     * Parse un JSON en toute sécurité.
     *
     * @param {string} str
     * @returns {Array}
     */
    function safeParse(str) {
        try {
            return JSON.parse(str || '[]');
        } catch (e) {
            console.warn('[PHOVE Dashboard] Erreur de parsing JSON :', e);
            return [];
        }
    }

    /**
     * Formate un nombre avec séparateurs de milliers.
     *
     * @param {number} value
     * @returns {string}
     */
    function formatMoney(value) {
        const num = Number(value) || 0;
        return num
            .toLocaleString('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            })
            .replace(/\u202f/g, ' ');
    }

    /**
     * Anime un nombre de 0 à la valeur cible.
     *
     * @param {HTMLElement} element
     * @param {number} from
     * @param {number} to
     * @param {number} duration
     */
    function animateNumber(element, from, to, duration) {
        const startTime = performance.now();
        const range = to - from;

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(from + range * eased);

            element.textContent = current;

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                element.textContent = to;
            }
        }

        requestAnimationFrame(step);
    }

    /**
     * Affiche un état vide dans un canvas.
     *
     * @param {HTMLCanvasElement} canvas
     * @param {string} message
     */
    function renderEmptyState(canvas, message) {
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;

        ctx.clearRect(0, 0, width, height);
        ctx.fillStyle = CONFIG.colors.muted;
        ctx.font = '13px ' + CONFIG.font;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(message, width / 2, height / 2);
    }

    /**
     * Affiche une bannière dans la console.
     */
    function logBanner() {
        console.log(
            '%c📊 PHOVE %c— Dashboard Administrateur chargé',
            'color: #1A5B9C; font-weight: bold; font-size: 14px;',
            'color: #64748B; font-size: 12px;'
        );
        console.log(
            '%cChart.js — Donut | Barres | Ligne | Horizontal',
            'color: #10B981; font-size: 11px;'
        );
    }


    /* ============================================================
       10. RAFRAÎCHISSEMENT AUTOMATIQUE (OPTIONNEL)
       ============================================================ */

    /**
     * Recharge le tableau de bord toutes les 5 minutes.
     * Décommenter pour activer.
     */
    // setInterval(function () {
    //     if (document.visibilityState === 'visible') {
    //         window.location.reload();
    //     }
    // }, 5 * 60 * 1000);


    /* ============================================================
       11. RACCOURCIS CLAVIER
       ============================================================ */

    document.addEventListener('keydown', function (event) {
        const tag = (event.target && event.target.tagName) || '';

        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') {
            return;
        }

        // Ctrl + D → Tableau de bord
        if ((event.ctrlKey || event.metaKey) && (event.key === 'd' || event.key === 'D')) {
            event.preventDefault();
            window.location.href = '/admin/dashboard';
        }
    });

})();