/* ==========================================================
   Scripts : date du pied de page, menu actif, validations
   ========================================================== */

/* Date affichée dans le pied de page (index.html) */
function datej() {
  const el = document.getElementById("date");
  if (!el) return;
  const mois = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet",
    "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
  const d = new Date();
  el.textContent = d.getDate() + " " + mois[d.getMonth()] + " " + d.getFullYear();
}

/* Menu : met en évidence la page ouverte */
function menuActif() {
  const liens = document.querySelectorAll(".menu a");
  liens.forEach(function (lien) {
    lien.addEventListener("click", function () {
      liens.forEach(function (l) { l.classList.remove("actif"); });
      lien.classList.add("actif");
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  datej();
  menuActif();
  /* Date de début par défaut : aujourd'hui */
  const da = document.getElementById("da");
  if (da && !da.value) da.value = new Date().toISOString().slice(0, 10);
});

/* ---------- Aide à la validation ---------- */
function erreur(idChamp, message) {
  const boite = document.getElementById("alerte");
  document.querySelectorAll("input.invalide").forEach(function (i) {
    i.classList.remove("invalide");
  });
  const champ = document.getElementById(idChamp);
  if (champ) {
    champ.classList.add("invalide");
    champ.focus();
  }
  if (boite) {
    boite.textContent = message;
    boite.classList.add("visible");
  }
  return false;
}

function efface() {
  const boite = document.getElementById("alerte");
  if (boite) boite.classList.remove("visible");
  document.querySelectorAll("input.invalide").forEach(function (i) {
    i.classList.remove("invalide");
  });
}

const chiffres = /^[0-9]+$/;

function serieValide(v) {
  return chiffres.test(v) && v.length === 3 && parseInt(v, 10) > 0 && parseInt(v, 10) < 150;
}
function numeroValide(v) {
  return chiffres.test(v) && v.length === 4 && parseInt(v, 10) > 0;
}
function val(id) {
  return document.getElementById(id).value.trim();
}

/* ---------- Formulaire client ---------- */
function verif1() {
  efface();
  if (!chiffres.test(val("cin")) || val("cin").length !== 8)
    return erreur("cin", "Le CIN doit contenir exactement 8 chiffres.");
  if (val("nom") === "") return erreur("nom", "Saisissez le nom du client.");
  if (val("pr") === "") return erreur("pr", "Saisissez le prénom du client.");
  if (val("email") === "" || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val("email")))
    return erreur("email", "Saisissez une adresse e-mail valide.");
  return true;
}

/* ---------- Formulaire voiture ---------- */
function verif2() {
  efface();
  if (!serieValide(val("loc")))
    return erreur("loc", "La série doit contenir 3 chiffres et être inférieure à 150.");
  if (!numeroValide(val("tn")))
    return erreur("tn", "Le numéro doit contenir 4 chiffres.");
  if (val("type") === "") return erreur("type", "Saisissez le modèle de la voiture.");
  const an = val("an");
  if (!chiffres.test(an) || parseInt(an, 10) <= 1990 || parseInt(an, 10) > new Date().getFullYear() + 1)
    return erreur("an", "L'année doit être comprise entre 1991 et " + (new Date().getFullYear() + 1) + ".");
  const prix = val("prix").replace(",", ".");
  if (prix === "" || isNaN(prix) || parseFloat(prix) <= 0 || parseFloat(prix) >= 1000)
    return erreur("prix", "Le prix doit être un nombre positif inférieur à 1000.");
  document.getElementById("prix").value = prix;
  return true;
}

/* ---------- Formulaire location ---------- */
function verif3() {
  efface();
  if (!serieValide(val("mat")))
    return erreur("mat", "La série doit contenir 3 chiffres et être inférieure à 150.");
  if (!numeroValide(val("mat2")))
    return erreur("mat2", "Le numéro doit contenir 4 chiffres.");
  if (!chiffres.test(val("ncin")) || val("ncin").length !== 8)
    return erreur("ncin", "Le CIN doit contenir exactement 8 chiffres.");
  const date = val("da");
  const aujourdhui = new Date().toISOString().slice(0, 10);
  if (date === "" || date < aujourdhui)
    return erreur("da", "La date de début ne peut pas être passée.");
  const j = val("nbj");
  if (!chiffres.test(j) || parseInt(j, 10) <= 0 || parseInt(j, 10) > 365)
    return erreur("nbj", "Le nombre de jours doit être compris entre 1 et 365.");
  return true;
}
