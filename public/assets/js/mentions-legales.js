/**
 * AHD-RDC — SCRIPTS PAGE MENTIONS LÉGALES
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES BLOCS D'ARTICLES — EFFET CASCADE
    // ============================================================
    const legalBlocks = document.querySelectorAll('.legal-block');

    if (legalBlocks.length > 0 && 'IntersectionObserver' in window) {
        const blockObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const block = entry.target;

                    setTimeout(() => {
                        block.classList.add('animated');
                    }, index * 80);

                    blockObserver.unobserve(block);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        legalBlocks.forEach(block => {
            blockObserver.observe(block);
        });
    }

    // ============================================================
    // 2. ANIMATION DES NUMÉROS D'ARTICLES AU SURVOL
    // ============================================================
    const legalNumbers = document.querySelectorAll('.legal-number');

    legalNumbers.forEach(num => {
        num.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.15) rotate(10deg)';
            this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        num.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'transform 0.3s ease';
        });
    });

    // ============================================================
    // 3. ANIMATION DE LA CARTE D'INFORMATIONS
    // ============================================================
    const legalInfoCards = document.querySelectorAll('.legal-info-card');

    if (legalInfoCards.length > 0 && 'IntersectionObserver' in window) {
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateX(0)';
                    cardObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -20px 0px'
        });

        legalInfoCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateX(-15px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            cardObserver.observe(card);
        });
    }

    // ============================================================
    // 4. ANIMATION DES ÉLÉMENTS DE LA LISTE
    // ============================================================
    const checkListItems = document.querySelectorAll('.check-list li');

    if (checkListItems.length > 0 && 'IntersectionObserver' in window) {
        const listObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const item = entry.target;

                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, index * 60);

                    listObserver.unobserve(item);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -10px 0px'
        });

        checkListItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-10px)';
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            listObserver.observe(item);
        });
    }

    // ============================================================
    // 5. ANIMATION DE L'AVERTISSEMENT
    // ============================================================
    const legalWarning = document.querySelector('.legal-warning');

    if (legalWarning && 'IntersectionObserver' in window) {
        const warningObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'scale(1)';
                    warningObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.3
        });

        legalWarning.style.opacity = '0';
        legalWarning.style.transform = 'scale(0.95)';
        legalWarning.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        warningObserver.observe(legalWarning);
    }

    // ============================================================
    // 6. ANIMATION DE LA CARTE DE CONTACT
    // ============================================================
    const legalContactCard = document.querySelector('.legal-contact-card');

    if (legalContactCard && 'IntersectionObserver' in window) {
        const contactObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    contactObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.3
        });

        legalContactCard.style.opacity = '0';
        legalContactCard.style.transform = 'translateY(15px)';
        legalContactCard.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        contactObserver.observe(legalContactCard);
    }

    // ============================================================
    // 7. MISE À JOUR AUTOMATIQUE DE LA DATE
    // ============================================================
    const updateDate = document.querySelector('.legal-update span');

    if (updateDate) {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        const formattedDate = `${day}/${month}/${year}`;

        if (updateDate.textContent.includes('Dernière mise à jour')) {
            updateDate.textContent = `Dernière mise à jour : ${formattedDate}`;
        }
    }

    // ============================================================
    // 8. SMOOTH SCROLL VERS LES ARTICLES (ANCRES)
    // ============================================================
    const hash = window.location.hash;

    if (hash) {
        const targetElement = document.querySelector(hash);

        if (targetElement) {
            setTimeout(() => {
                const headerHeight = document.getElementById('header')?.offsetHeight || 90;
                const topbarHeight = document.getElementById('topbar')?.offsetHeight || 44;
                const totalOffset = headerHeight + topbarHeight + 30;

                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - totalOffset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }, 400);
        }
    }

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c⚖️ AHD-RDC %c— Page Mentions légales chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});