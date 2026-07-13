/**
 * AHD-RDC — SCRIPTS PAGE FAIRE UN DON
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
// 1. SÉLECTION DES MONTANTS SUGGÉRÉS
// ============================================================
const montantCards = document.querySelectorAll('.don-montant-card:not(.don-montant-card-custom)');
const customCard = document.getElementById('customMontantCard');
const customRadio = document.getElementById('customRadio');
const customInput = document.getElementById('customMontant');
const customWrapper = document.getElementById('customMontantWrapper');
const recapMontant = document.getElementById('recapMontant');
const hiddenMontant = document.getElementById('hiddenMontant');
let selectedMontant = 50;

montantCards.forEach(card => {
    card.addEventListener('click', function () {
        // Désélectionner toutes les cartes
        montantCards.forEach(c => c.classList.remove('selected'));
        customCard.classList.remove('selected');
        customRadio.checked = false;
        customInput.disabled = true;
        customWrapper.classList.remove('visible');
        customInput.value = '';

        // Sélectionner la carte cliquée
        this.classList.add('selected');
        const radio = this.querySelector('.don-montant-radio');
        radio.checked = true;

        selectedMontant = parseInt(this.getAttribute('data-montant'));
        
        // Mettre à jour le champ caché
        if (hiddenMontant) {
            hiddenMontant.value = selectedMontant;
        }

        // Remettre le type à suggere
        const hiddenMontantType = document.querySelector('input[name="montant_type"][type="hidden"]');
        if (hiddenMontantType) {
            hiddenMontantType.value = 'suggere';
        }
        
        updateRecap();
    });
});

// Carte "Autre montant"
if (customCard && customRadio && customInput && customWrapper) {
    customCard.addEventListener('click', function () {
        montantCards.forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        customRadio.checked = true;
        customInput.disabled = false;
        customWrapper.classList.add('visible');
        customInput.focus();

        // Mettre à jour le type de montant
        const hiddenMontantType = document.querySelector('input[name="montant_type"][type="hidden"]');
        if (hiddenMontantType) {
            hiddenMontantType.value = 'personnalise';
        }
    });

    customInput.addEventListener('input', function () {
        selectedMontant = parseFloat(this.value) || 0;
        
        // Mettre à jour le champ caché
        if (hiddenMontant) {
            hiddenMontant.value = selectedMontant;
        }
        
        updateRecap();
    });
}

    // ============================================================
    // 2. SÉLECTION DU TYPE DE DON
    // ============================================================
    const typeCards = document.querySelectorAll('.don-type-card');
    const recapType = document.getElementById('recapType');

    typeCards.forEach(card => {
        card.addEventListener('click', function () {
            typeCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('.don-type-radio');
            radio.checked = true;
            updateRecap();
        });
    });

    // ============================================================
    // 3. SÉLECTION DE L'AFFECTATION
    // ============================================================
    const affectationCards = document.querySelectorAll('.don-affectation-card');

    affectationCards.forEach(card => {
        card.addEventListener('click', function () {
            affectationCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('.don-affectation-radio');
            radio.checked = true;
        });
    });

    // ============================================================
    // 4. SÉLECTION DU MODE DE PAIEMENT
    // ============================================================
    const paiementCards = document.querySelectorAll('.don-paiement-card');
    const instructionCards = document.querySelectorAll('.don-instruction-card');
    const recapPaiement = document.getElementById('recapPaiement');

    paiementCards.forEach(card => {
        card.addEventListener('click', function () {
            paiementCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('.don-paiement-radio');
            radio.checked = true;

            // Afficher les instructions correspondantes
            const paiementValue = radio.value;
            instructionCards.forEach(inst => {
                inst.style.display = 'none';
            });
            const activeInstruction = document.querySelector(`.don-instruction-card[data-paiement="${paiementValue}"]`);
            if (activeInstruction) {
                activeInstruction.style.display = 'block';
            }

            updateRecap();
        });
    });

    // ============================================================
    // 5. MISE À JOUR DU RÉCAPITULATIF
    // ============================================================
    function updateRecap() {
        // Montant
        if (recapMontant) {
            recapMontant.textContent = selectedMontant.toLocaleString('fr-FR') + ' $';
        }

        // Type
        if (recapType) {
            const typeRadio = document.querySelector('input[name="type_don"]:checked');
            if (typeRadio) {
                recapType.textContent = typeRadio.value === 'recurrent' ? 'Don récurrent mensuel' : 'Don ponctuel';
            }
        }

        // Paiement
        if (recapPaiement) {
            const paiementRadio = document.querySelector('input[name="mode_paiement"]:checked');
            if (paiementRadio) {
                const paiementLabels = {
                    'mobile_money': 'Mobile Money',
                    'virement_bancaire': 'Virement bancaire',
                    'carte_bancaire': 'Carte bancaire',
                    'paypal': 'PayPal',
                    'especes': 'Espèces'
                };
                recapPaiement.textContent = paiementLabels[paiementRadio.value] || paiementRadio.value;
            }
        }
    }

    // Initialiser le récapitulatif
    updateRecap();

    // ============================================================
// 6. VALIDATION DU FORMULAIRE
// ============================================================
const donForm = document.getElementById('donForm');
const donSubmitBtn = document.getElementById('donSubmitBtn');

if (donForm && donSubmitBtn) {
    donForm.addEventListener('submit', function (e) {
        // Vérifier le montant personnalisé si sélectionné
        if (customRadio && customRadio.checked) {
            const customValue = parseFloat(customInput.value);
            if (!customValue || customValue < 1 || customValue > 50000) {
                e.preventDefault();
                customInput.classList.add('error');
                customInput.focus();

                const existingError = customInput.parentElement.querySelector('.don-error');
                if (!existingError) {
                    const error = document.createElement('span');
                    error.className = 'don-error';
                    error.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez entrer un montant valide (entre 1 et 50 000 $)';
                    customInput.parentElement.appendChild(error);
                }
                return;
            }
            
            // Mettre à jour le champ caché avec le montant personnalisé
            if (hiddenMontant) {
                hiddenMontant.value = customValue;
            }
        }

        // Vérifier les conditions
        const conditionsCheckbox = document.querySelector('input[name="conditions_acceptees"]');
        if (conditionsCheckbox && !conditionsCheckbox.checked) {
            e.preventDefault();
            alert('Veuillez accepter les conditions pour continuer.');
            return;
        }

        // Afficher le loader
        donSubmitBtn.classList.add('btn-loading');
        donSubmitBtn.disabled = true;
    });
}
    // ============================================================
    // 7. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.don-input, .don-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.classList.remove('error');
            const errorSpan = this.parentElement.querySelector('.don-error');
            if (errorSpan) {
                errorSpan.remove();
            }
        });
    });

    // ============================================================
    // 8. ANIMATION DES ÉTAPES AU SCROLL
    // ============================================================
    const donSteps = document.querySelectorAll('.don-step');

    if (donSteps.length > 0 && 'IntersectionObserver' in window) {
        const stepObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const step = entry.target;

                    setTimeout(() => {
                        step.classList.add('visible');
                    }, index * 150);

                    stepObserver.unobserve(step);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        donSteps.forEach(step => {
            stepObserver.observe(step);
        });
    }

    // ============================================================
    // 9. ANIMATION DES CARTES IMPACT
    // ============================================================
    const impactCards = document.querySelectorAll('.don-impact-card');

    if (impactCards.length > 0 && 'IntersectionObserver' in window) {
        const impactObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);

                    impactObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -20px 0px'
        });

        impactCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            impactObserver.observe(card);
        });
    }

    // ============================================================
    // 10. INITIALISATION
    // ============================================================
    console.log(
        '%c❤️ AHD-RDC %c— Page Faire un don chargée',
        'color: #D32F2F; font-weight: bold;',
        'color: #777;'
    );

});