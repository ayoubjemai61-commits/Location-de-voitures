<?php
require 'connexion.php';

$serie  = post('loc');
$numero = post('tn');
$type   = post('type');
$an     = post('an');
$prix   = str_replace(',', '.', post('prix'));
$retour = 'ajoutervoiture.html';

if (!preg_match('/^[0-9]{3}$/', $serie) || (int)$serie <= 0 || (int)$serie >= 150
    || !preg_match('/^[0-9]{4}$/', $numero)
    || $type === ''
    || !preg_match('/^[0-9]{4}$/', $an) || (int)$an <= 1990
    || !is_numeric($prix) || (float)$prix <= 0 || (float)$prix >= 1000) {
    afficher('erreur', 'Données invalides', 'Vérifiez l\'immatriculation, le modèle, l\'année et le prix.', $retour);
}

$immat = $serie . 'tn' . $numero;

$st = mysqli_prepare($con, 'SELECT immat FROM voiture WHERE immat = ?');
mysqli_stmt_bind_param($st, 's', $immat);
mysqli_stmt_execute($st);
mysqli_stmt_store_result($st);
if (mysqli_stmt_num_rows($st) >= 1) {
    afficher('erreur', 'Voiture existante', 'La voiture ' . $immat . ' est déjà enregistrée.', $retour);
}
mysqli_stmt_close($st);

$dispo = 'D';
$an    = (int)$an;
$prix  = (float)$prix;
$st = mysqli_prepare($con, 'INSERT INTO voiture (immat, typevoiture, annee, prixloca, disponiblite) VALUES (?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($st, 'ssids', $immat, $type, $an, $prix, $dispo);
if (!mysqli_stmt_execute($st)) {
    afficher('erreur', 'Échec de l\'enregistrement', 'La voiture n\'a pas pu être ajoutée.', $retour);
}

afficher('ok', 'Voiture ajoutée', 'La voiture ' . $immat . ' est maintenant disponible à la location.', $retour);
