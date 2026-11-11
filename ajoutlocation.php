<?php
require 'connexion.php';

$serie  = post('mat');
$numero = post('mat2');
$cin    = post('ncin');
$debut  = post('da');
$nbj    = post('nbj');
$retour = 'ajouterlocation.html';

$dateOk = DateTime::createFromFormat('Y-m-d', $debut);
if (!preg_match('/^[0-9]{3}$/', $serie) || !preg_match('/^[0-9]{4}$/', $numero)
    || !preg_match('/^[0-9]{8}$/', $cin)
    || !$dateOk || $dateOk->format('Y-m-d') !== $debut || $debut < date('Y-m-d')
    || !ctype_digit($nbj) || (int)$nbj < 1 || (int)$nbj > 365) {
    afficher('erreur', 'Données invalides', 'Vérifiez l\'immatriculation, le CIN, la date et le nombre de jours.', $retour);
}
$nbj   = (int)$nbj;
$immat = $serie . 'tn' . $numero;

/* Voiture */
$st = mysqli_prepare($con, 'SELECT disponiblite FROM voiture WHERE immat = ?');
mysqli_stmt_bind_param($st, 's', $immat);
mysqli_stmt_execute($st);
mysqli_stmt_bind_result($st, $dispo);
$voitureTrouvee = mysqli_stmt_fetch($st);
mysqli_stmt_close($st);
if (!$voitureTrouvee) {
    afficher('erreur', 'Voiture introuvable', 'Aucune voiture n\'a l\'immatriculation ' . $immat . '.', $retour);
}
if ($dispo === 'N') {
    afficher('erreur', 'Voiture indisponible', 'La voiture ' . $immat . ' est déjà en location.', $retour);
}

/* Client */
$st = mysqli_prepare($con, 'SELECT idclient FROM client WHERE cin = ?');
mysqli_stmt_bind_param($st, 's', $cin);
mysqli_stmt_execute($st);
mysqli_stmt_bind_result($st, $idclient);
$clientTrouve = mysqli_stmt_fetch($st);
mysqli_stmt_close($st);
if (!$clientTrouve) {
    afficher('erreur', 'Client introuvable', 'Aucun client n\'a le CIN ' . $cin . '. Ajoutez-le d\'abord.', $retour);
}

/* Location + changement de disponibilité dans une seule transaction */
mysqli_begin_transaction($con);

$st = mysqli_prepare($con, 'INSERT INTO location (immat, idclient, dateloca, dateres) VALUES (?, ?, ?, DATE_ADD(?, INTERVAL ? DAY))');
mysqli_stmt_bind_param($st, 'sissi', $immat, $idclient, $debut, $debut, $nbj);
$ok1 = mysqli_stmt_execute($st);
mysqli_stmt_close($st);

$st = mysqli_prepare($con, "UPDATE voiture SET disponiblite = 'N' WHERE immat = ?");
mysqli_stmt_bind_param($st, 's', $immat);
$ok2 = mysqli_stmt_execute($st);
mysqli_stmt_close($st);

if (!$ok1 || !$ok2) {
    mysqli_rollback($con);
    afficher('erreur', 'Échec de l\'enregistrement', 'La location n\'a pas pu être enregistrée.', $retour);
}
mysqli_commit($con);

afficher('ok', 'Location enregistrée', 'La voiture ' . $immat . ' est louée pour ' . $nbj . ' jour(s) à partir du ' . date('d/m/Y', strtotime($debut)) . '.', $retour);
