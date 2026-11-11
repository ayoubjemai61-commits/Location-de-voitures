<?php
/* Connexion à la base + affichage des messages (inclus par les autres pages PHP) */
mysqli_report(MYSQLI_REPORT_OFF);

function afficher($type, $titre, $texte, $retour = '')
{
    $icones = ['ok' => '✓', 'erreur' => '!', 'info' => 'i'];
    $titre = htmlspecialchars($titre);
    $texte = htmlspecialchars($texte);
    $lien  = $retour !== ''
        ? '<div class="actions"><a class="bt" href="' . htmlspecialchars($retour) . '">Retour au formulaire</a></div>'
        : '';
    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>$titre</title>
  <link rel="stylesheet" href="mesStyle.css">
</head>
<body class="page">
  <div class="carte"><div class="carte__corps">
    <div class="resultat resultat--$type">
      <div class="resultat__icone" aria-hidden="true">{$icones[$type]}</div>
      <div><h2>$titre</h2><p>$texte</p></div>
    </div>
    $lien
  </div></div>
</body>
</html>
HTML;
    exit;
}

$con = mysqli_connect('localhost', 'root', '', 'bd_location');
if (!$con) {
    afficher('erreur', 'Connexion impossible', 'La base de données est inaccessible. Vérifiez que MySQL est démarré.');
}
mysqli_set_charset($con, 'utf8mb4');

/* Lecture d'un champ POST nettoyé */
function post($nom)
{
    return isset($_POST[$nom]) ? trim($_POST[$nom]) : '';
}
