<?php
require 'connexion.php';

$cin   = post('cin');
$nom   = post('nom');
$pr    = post('pr');
$ad    = post('ad');
$email = post('email');
$retour = 'ajouterclient.html';

if (!preg_match('/^[0-9]{8}$/', $cin) || $nom === '' || $pr === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    afficher('erreur', 'Données invalides', 'Vérifiez le CIN, le nom, le prénom et l\'adresse e-mail.', $retour);
}

/* Le client existe-t-il déjà ? */
$st = mysqli_prepare($con, 'SELECT idclient FROM client WHERE cin = ?');
mysqli_stmt_bind_param($st, 's', $cin);
mysqli_stmt_execute($st);
mysqli_stmt_store_result($st);
if (mysqli_stmt_num_rows($st) >= 1) {
    afficher('erreur', 'Client existant', 'Un client avec le CIN ' . $cin . ' est déjà enregistré.', $retour);
}
mysqli_stmt_close($st);

/* Insertion */
$st = mysqli_prepare($con, 'INSERT INTO client (cin, nom, prenom, adresse, email) VALUES (?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($st, 'sssss', $cin, $nom, $pr, $ad, $email);
if (!mysqli_stmt_execute($st)) {
    afficher('erreur', 'Échec de l\'enregistrement', 'Le client n\'a pas pu être ajouté. Vérifiez la longueur des champs.', $retour);
}

/* Adresse masquée : ****@domaine.com */
$arobase = strpos($email, '@');
$masque  = str_repeat('*', $arobase) . substr($email, $arobase);

afficher('ok', 'Client ajouté', 'Un message de validation a été envoyé à ' . $masque . '.', $retour);
