/**
 * AHD-RDC — SCRIPTS PAGE TRANSPARENCE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES DE RAPPORTS FINANCIERS
    // ============================================================
    const rapportCards = document.querySelectorAll('.rapport-card');

    if (rapportCards.length > 0 && 'IntersectionObserver' in window) {
        const rapportObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 120);

                    rapportObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        rapportCards.forEach(card => {
            rapportObserver.observe(card);
        });
    }

    // ============================================================
    // 2. ANIMATION DES CARTES DE DOCUMENTS
    // ============================================================
    const documentCards = document.querySelectorAll('.document-card');

    if (documentCards.length > 0 && 'IntersectionObserver' in window) {
        const docObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 100);

                    docObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        documentCards.forEach(card => {
            docObserver.observe(card);
        });
    }

    // ============================================================
    // 3. ANIMATION DE LA SECTION ENGAGEMENT
    // ============================================================
    const engagementCard = document.querySelector('.engagement-card');

    if (engagementCard && 'IntersectionObserver' in window) {
        const engagementObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    engagementObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -30px 0px'
        });

        engagementCard.style.opacity = '0';
        engagementCard.style.transform = 'translateY(30px)';
        engagementCard.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        engagementObserver.observe(engagementCard);
    }

    // ============================================================
    // 4. ANIMATION DES VALEURS TRANSVERSE
    // ============================================================
    const valeurItems = document.querySelectorAll('.transparence-value-item');

    if (valeurItems.length > 0 && 'IntersectionObserver' in window) {
        const valeurObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const item = entry.target;

                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, index * 80);

                    valeurObserver.unobserve(item);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -10px 0px'
        });

        valeurItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(15px)';
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            valeurObserver.observe(item);
        });
    }

    // ============================================================
    // 5. EFFET DE SURVOL SUR LES CARTES RAPPORTS
    // ============================================================
    rapportCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.rapport-card-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.rapport-card-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 6. EFFET DE SURVOL SUR LES CARTES DOCUMENTS
    // ============================================================
    documentCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.document-card-icon');
            const action = this.querySelector('.document-card-action');

            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
            if (action) {
                action.style.transform = 'scale(1.1)';
                action.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.document-card-icon');
            const action = this.querySelector('.document-card-action');

            if (icon) icon.style.transform = 'scale(1)';
            if (action) action.style.transform = 'scale(1)';
        });
    });

    // ============================================================
    // 7. CONFIRMATION DE TÉLÉCHARGEMENT
    // ============================================================
    const downloadLinks = document.querySelectorAll('.rapport-download-btn, .document-card[href]');

    downloadLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const card = this.closest('.rapport-card') || this.closest('.document-card');
            
            if (card) {
                const title = card.querySelector('.rapport-card-title, .document-card-title');
                if (title) {
                    console.log('📥 Téléchargement : ' + title.textContent.trim());
                }
            }
        });
    });

    // ============================================================
    // 8. CONSERVER L'ONGLET ACTIF
    // ============================================================
    const tabs = document.querySelectorAll('.transparence-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c🔍 AHD-RDC %c— Page Transparence chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});