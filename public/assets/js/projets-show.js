/**
 * AHD-RDC — SCRIPTS PAGE DÉTAIL D'UN PROJET
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES D'INFORMATIONS
    // ============================================================
    const infoCards = document.querySelectorAll('.projet-info-card');

    if (infoCards.length > 0 && 'IntersectionObserver' in window) {
        const infoObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 100);

                    infoObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -20px 0px'
        });

        infoCards.forEach(card => {
            infoObserver.observe(card);
        });
    }

    // ============================================================
    // 2. ANIMATION DES BLOCS DE CONTENU
    // ============================================================
    const projetBlocks = document.querySelectorAll('.projet-block');

    if (projetBlocks.length > 0 && 'IntersectionObserver' in window) {
        const blockObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const block = entry.target;

                    setTimeout(() => {
                        block.classList.add('animated');
                    }, index * 120);

                    blockObserver.unobserve(block);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        projetBlocks.forEach(block => {
            blockObserver.observe(block);
        });
    }

    // ============================================================
    // 3. ANIMATION DES CARTES SIDEBAR
    // ============================================================
    const sidebarCards = document.querySelectorAll('.sidebar-card');

    if (sidebarCards.length > 0 && 'IntersectionObserver' in window) {
        const sidebarObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 150);

                    sidebarObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        sidebarCards.forEach(card => {
            sidebarObserver.observe(card);
        });
    }

    // ============================================================
    // 4. ANIMATION DU CERCLE DE PROGRESSION BUDGET
    // ============================================================
    const budgetCircle = document.querySelector('.budget-circle-fill');

    if (budgetCircle && 'IntersectionObserver' in window) {
        const circleObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const percent = budgetCircle.parentElement.parentElement.getAttribute('data-percent') || 0;
                    budgetCircle.setAttribute('stroke-dasharray', '0, 100');

                    setTimeout(() => {
                        budgetCircle.setAttribute('stroke-dasharray', percent + ', 100');
                        budgetCircle.style.transition = 'stroke-dasharray 1.5s ease';
                    }, 300);

                    circleObserver.unobserve(budgetCircle);
                }
            });
        }, {
            threshold: 0.5
        });

        circleObserver.observe(budgetCircle);
    }

    // ============================================================
    // 5. ANIMATION DES BARRES KPI
    // ============================================================
    const kpiBars = document.querySelectorAll('.kpi-fill');

    if (kpiBars.length > 0 && 'IntersectionObserver' in window) {
        const kpiObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const targetWidth = bar.getAttribute('data-width') || bar.style.width || '0%';

                    bar.style.width = '0%';
                    bar.style.transition = 'width 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)';

                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 200);

                    kpiObserver.unobserve(bar);
                }
            });
        }, {
            threshold: 0.5
        });

        kpiBars.forEach(bar => kpiObserver.observe(bar));
    }

    // ============================================================
    // 6. ANIMATION DES TÉMOIGNAGES
    // ============================================================
    const temoignageCards = document.querySelectorAll('.temoignage-card');

    if (temoignageCards.length > 0 && 'IntersectionObserver' in window) {
        const temoignageObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 120);

                    temoignageObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        temoignageCards.forEach(card => {
            temoignageObserver.observe(card);
        });
    }

    // ============================================================
    // 7. GALERIE LIGHTBOX
    // ============================================================
    const galleryItems = document.querySelectorAll('.gallery-item');
    let lightbox = null;

    function createLightbox() {
        lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-overlay"></div>
            <div class="lightbox-container">
                <button class="lightbox-close" title="Fermer">
                    <i class="fas fa-times"></i>
                </button>
                <button class="lightbox-prev" title="Précédent">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="lightbox-content">
                    <img src="" alt="" class="lightbox-image" id="lightboxImage">
                    <div class="lightbox-caption"></div>
                </div>
                <button class="lightbox-next" title="Suivant">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="lightbox-counter"></div>
            </div>
        `;

        document.body.appendChild(lightbox);

        // Fermer la lightbox
        lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
        lightbox.querySelector('.lightbox-overlay').addEventListener('click', closeLightbox);

        // Navigation
        lightbox.querySelector('.lightbox-prev').addEventListener('click', prevImage);
        lightbox.querySelector('.lightbox-next').addEventListener('click', nextImage);

        // Touches clavier
        document.addEventListener('keydown', handleLightboxKeys);
    }

    let currentImageIndex = 0;
    let galleryImages = [];

    if (galleryItems.length > 0) {
        createLightbox();

        galleryImages = Array.from(galleryItems).map(item => ({
            src: item.getAttribute('data-src'),
            title: item.getAttribute('data-title') || '',
        }));

        galleryItems.forEach((item, index) => {
            item.addEventListener('click', function () {
                openLightbox(index);
            });
        });
    }

    function openLightbox(index) {
        currentImageIndex = index;
        updateLightboxImage();
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function updateLightboxImage() {
        const image = lightbox.querySelector('#lightboxImage');
        const caption = lightbox.querySelector('.lightbox-caption');
        const counter = lightbox.querySelector('.lightbox-counter');

        image.src = galleryImages[currentImageIndex].src;
        image.alt = galleryImages[currentImageIndex].title;
        caption.textContent = galleryImages[currentImageIndex].title;
        counter.textContent = (currentImageIndex + 1) + ' / ' + galleryImages.length;
    }

    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        updateLightboxImage();
    }

    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        updateLightboxImage();
    }

    function handleLightboxKeys(e) {
        if (!lightbox.classList.contains('active')) return;

        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            prevImage();
        } else if (e.key === 'ArrowRight') {
            nextImage();
        }
    }

    // Ajouter les styles de la lightbox dynamiquement
    const lightboxStyles = document.createElement('style');
    lightboxStyles.textContent = `
        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .lightbox.active {
            opacity: 1;
            visibility: visible;
        }
        .lightbox-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.92);
            cursor: pointer;
        }
        .lightbox-container {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lightbox-content {
            position: relative;
            max-width: 85%;
            max-height: 85%;
            text-align: center;
        }
        .lightbox-image {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        .lightbox-caption {
            color: #fff;
            font-size: 14px;
            margin-top: 12px;
            text-align: center;
            opacity: 0.7;
        }
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 2;
        }
        .lightbox-close:hover {
            background: var(--color-accent, #D32F2F);
            border-color: var(--color-accent, #D32F2F);
        }
        .lightbox-prev,
        .lightbox-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 2;
        }
        .lightbox-prev:hover,
        .lightbox-next:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        .lightbox-prev { left: 20px; }
        .lightbox-next { right: 20px; }
        .lightbox-counter {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            letter-spacing: 1px;
        }
        @media (max-width: 768px) {
            .lightbox-prev { left: 10px; }
            .lightbox-next { right: 10px; }
            .lightbox-prev,
            .lightbox-next {
                width: 38px;
                height: 38px;
                font-size: 14px;
            }
            .lightbox-close {
                width: 38px;
                height: 38px;
                font-size: 16px;
                top: 10px;
                right: 10px;
            }
        }
    `;
    document.head.appendChild(lightboxStyles);

    // ============================================================
    // 8. BOUTON PARTAGER (Web Share API)
    // ============================================================
    const shareButtons = document.querySelectorAll('.btn-share');

    shareButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url') || window.location.href;
            const title = this.getAttribute('data-title') || document.title;
            const text = this.getAttribute('data-text') || '';

            if (navigator.share) {
                navigator.share({ title, text, url }).catch(() => {});
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Lien copié !';
                    btn.style.color = 'var(--color-success)';
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.color = '';
                    }, 2000);
                }).catch(() => {});
            }
        });
    });

    // ============================================================
    // 9. BOUTON IMPRIMER
    // ============================================================
    const printButtons = document.querySelectorAll('.btn-print');

    printButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            window.print();
        });
    });

    // ============================================================
    // 10. EFFET DE SURVOL SUR LES IMAGES DE LA GALERIE
    // ============================================================
    galleryItems.forEach(item => {
        item.addEventListener('mouseenter', function () {
            const thumb = this.querySelector('.gallery-thumb');
            if (thumb) {
                thumb.style.transform = 'scale(1.08)';
                thumb.style.transition = 'transform 0.3s ease';
            }
        });

        item.addEventListener('mouseleave', function () {
            const thumb = this.querySelector('.gallery-thumb');
            if (thumb) {
                thumb.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 11. INITIALISATION
    // ============================================================
    console.log(
        '%c📋 AHD-RDC %c— Page Détail Projet chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});