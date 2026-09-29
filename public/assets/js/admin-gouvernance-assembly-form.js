/* ============================================================
   ASSEMBLÉE GÉNÉRALE — FORMULAIRE
   ------------------------------------------------------------
   Calcul automatique du quorum + affichage dynamique.
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    const membresTotal    = document.getElementById('membres_total');
    const membresPresents = document.getElementById('membres_presents');
    const membresAbsents  = document.getElementById('membres_absents');
    const quorumInfo      = document.getElementById('quorumInfo');

    if (!membresTotal || !membresPresents || !quorumInfo) {
        return;
    }

    /**
     * Recalcule le quorum et met à jour l'affichage.
     */
    function updateQuorum() {
        const total    = parseInt(membresTotal.value, 10)    || 0;
        const presents = parseInt(membresPresents.value, 10) || 0;

        // État : vide
        if (total <= 0) {
            quorumInfo.dataset.state = 'empty';
            quorumInfo.innerHTML = '<i class="fas fa-info-circle"></i> Remplissez les champs pour voir le quorum';
            return;
        }

        const quorum         = total / 2;
        const quorumAtteint  = presents >= quorum;
        const pourcentage    = Math.round((presents / total) * 100);
        const majoriteSimple = Math.ceil(quorum);

        // Mise à jour de l'état visuel
        quorumInfo.dataset.state = quorumAtteint ? 'success' : 'warning';

        // Construction du message
        const icon    = quorumAtteint ? 'check-circle' : 'times-circle';
        const message = quorumAtteint ? 'Quorum atteint' : 'Quorum non atteint';

        quorumInfo.innerHTML =
            '<i class="fas fa-' + icon + '"></i> ' +
            message + ' : ' +
            presents + '/' + total + ' présents (' + pourcentage + '%)<br>' +
            '<small>Majorité simple requise : ' + majoriteSimple + ' membres</small>';

        // Mise à jour automatique des absents
        if (membresAbsents && !membresAbsents.dataset.manualEdit) {
            const absents = Math.max(0, total - presents);
            membresAbsents.value = absents;
        }
    }

    /**
     * Recalcule les présents si les absents sont modifiés manuellement.
     */
    function updatePresentsFromAbsents() {
        const total   = parseInt(membresTotal.value, 10)   || 0;
        const absents = parseInt(membresAbsents.value, 10) || 0;

        if (total > 0 && membresPresents && !membresPresents.dataset.manualEdit) {
            const presents = Math.max(0, total - absents);
            membresPresents.value = presents;
            updateQuorum();
        }
    }

    // Écouteurs d'événements
    membresTotal.addEventListener('input', updateQuorum);
    membresPresents.addEventListener('input', updateQuorum);

    if (membresAbsents) {
        membresAbsents.addEventListener('input', function () {
            this.dataset.manualEdit = '1';
            updatePresentsFromAbsents();
        });
    }

    // Calcul initial
    updateQuorum();
});