/* ============================================================
   Talent SKULLVI By JM — Script client
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('formCandidat');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        const errors = [];

        const nom    = form.nom.value.trim();
        const prenom = form.prenom.value.trim();
        const email  = form.email.value.trim();
        const niveau = form.niveau_etude.value;
        const exp    = parseInt(form.experience.value, 10);
        const comp   = form.competences.value.trim();
        const motiv  = form.motivation.value;
        const dispo  = form.disponibilite.value;

        if (nom.length < 2)    errors.push("Nom trop court.");
        if (prenom.length < 2) errors.push("Prénom trop court.");
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push("Email invalide.");
        if (!niveau)           errors.push("Choisissez un niveau d'étude.");
        if (isNaN(exp) || exp < 0 || exp > 50) errors.push("Expérience entre 0 et 50.");
        if (comp.length < 2)   errors.push("Renseignez vos compétences.");
        if (!motiv)            errors.push("Choisissez votre motivation.");
        if (!dispo)            errors.push("Choisissez votre disponibilité.");

        if (errors.length > 0) {
            e.preventDefault();
            alert("Veuillez corriger :\n\n• " + errors.join("\n• "));
        }
    });

});