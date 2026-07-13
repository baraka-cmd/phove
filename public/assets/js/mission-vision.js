/**
 * AHD-RDC — SCRIPTS PAGE MISSION & VISION
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES MISSION AU SCROLL
    // ============================================================
    const missionCards = document.querySelectorAll('.mission-card');

    if (missionCards.length > 0 && 'IntersectionObserver' in window) {
        const missionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 120);

                    missionObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -30px 0px'
        });

        missionCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(40px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            missionObserver.observe(card);
        });
    }

    // ============================================================
    // 2. ANIMATION DE LA CARTE OBJECTIF GLOBAL
    // ============================================================
    const objectiveGlobalCard = document.querySelector('.objective-global-card');

    if (objectiveGlobalCard && 'IntersectionObserver' in window) {
        const objectiveObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    objectiveGlobalCard.style.opacity = '1';
                    objectiveGlobalCard.style.transform = 'scale(1)';
                    objectiveObserver.unobserve(objectiveGlobalCard);
                }
            });
        }, {
            threshold: 0.3
        });

        objectiveGlobalCard.style.opacity = '0';
        objectiveGlobalCard.style.transform = 'scale(0.9)';
        objectiveGlobalCard.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        objectiveObserver.observe(objectiveGlobalCard);
    }

    // ============================================================
    // 3. ANIMATION DES OBJECTIFS SPÉCIFIQUES — EFFET CASCADE
    // ============================================================
    const objectiveItems = document.querySelectorAll('.objective-item');

    if (objectiveItems.length > 0 && 'IntersectionObserver' in window) {
        const itemsObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const item = entry.target;

                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, index * 80);

                    itemsObserver.unobserve(item);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        objectiveItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-30px)';
            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            itemsObserver.observe(item);
        });
    }

    // ============================================================
    // 4. ANIMATION DE LA SECTION SLOGAN
    // ============================================================
    const sloganSection = document.querySelector('.slogan-section');

    if (sloganSection && 'IntersectionObserver' in window) {
        const sloganObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const text = sloganSection.querySelector('.slogan-text');
                    const subtext = sloganSection.querySelector('.slogan-subtext');

                    sloganSection.style.opacity = '1';
                    sloganSection.style.transform = 'translateY(0)';

                    if (text) {
                        text.style.opacity = '1';
                        text.style.transform = 'scale(1)';
                        text.style.transition = 'opacity 0.6s ease 0.2s, transform 0.6s ease 0.2s';
                    }

                    if (subtext) {
                        subtext.style.opacity = '1';
                        subtext.style.transition = 'opacity 0.6s ease 0.5s';
                    }

                    sloganObserver.unobserve(sloganSection);
                }
            });
        }, {
            threshold: 0.4
        });

        sloganSection.style.opacity = '0';
        sloganSection.style.transform = 'translateY(30px)';
        sloganSection.style.transition = 'opacity 0.6s ease, transform 0.6s ease';

        const sloganText = sloganSection.querySelector('.slogan-text');
        const sloganSubtext = sloganSection.querySelector('.slogan-subtext');

        if (sloganText) {
            sloganText.style.opacity = '0';
            sloganText.style.transform = 'scale(0.8)';
        }
        if (sloganSubtext) {
            sloganSubtext.style.opacity = '0';
        }

        sloganObserver.observe(sloganSection);
    }

    // ============================================================
    // 5. EFFET DE COMPTAGE SUR LES NUMÉROS DES OBJECTIFS
    // ============================================================
    const objectiveNumbers = document.querySelectorAll('.objective-number');

    objectiveNumbers.forEach(num => {
        num.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.2) rotate(10deg)';
            this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        num.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'transform 0.3s ease';
        });
    });

    // ============================================================
    // 6. ANIMATION DU BLOC VISION (CITATION)
    // ============================================================
    const visionQuote = document.querySelector('.vision-quote');

    if (visionQuote && 'IntersectionObserver' in window) {
        const quoteObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    visionQuote.style.opacity = '1';
                    visionQuote.style.transform = 'translateY(0)';
                    quoteObserver.unobserve(visionQuote);
                }
            });
        }, {
            threshold: 0.3
        });

        visionQuote.style.opacity = '0';
        visionQuote.style.transform = 'translateY(20px)';
        visionQuote.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        quoteObserver.observe(visionQuote);
    }

    // ============================================================
    // 7. INITIALISATION
    // ============================================================
    console.log(
        '%c🎯 AHD-RDC %c— Page Mission & Vision chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});