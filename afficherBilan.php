<?php
require 'connexion.php';

$sql = "SELECT v.immat,
               v.typevoiture,
               COUNT(*) AS nb,
               SUM(DATEDIFF(l.dateres, l.dateloca)) AS jours,
               SUM(DATEDIFF(l.dateres, l.dateloca)) * v.prixloca AS revenu
        FROM voiture v
        JOIN location l ON l.immat = v.immat
        GROUP BY v.immat, v.typevoiture, v.prixloca
        ORDER BY revenu DESC";
$res = mysqli_query($con, $sql);

$lignes = [];
$totalRevenu = 0;
$totalLocations = 0;
while ($t = mysqli_fetch_assoc($res)) {
    $lignes[] = $t;
    $totalRevenu += $t['revenu'];
    $totalLocations += $t['nb'];
}
function dt($n) { return number_format((float)$n, 3, ',', ' ') . ' DT'; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bilan des locations</title>
  <link rel="stylesheet" href="mesStyle.css">
</head>
<body class="page">
  <div class="carte carte--large">
    <div class="carte__tete">
      <h2>Bilan des locations</h2>
      <p>Revenu par voiture, de la plus rentable à la moins rentable.</p>
    </div>

<?php if (count($lignes) === 0): ?>
    <div class="carte__corps">
      <div class="resultat resultat--info">
        <div class="resultat__icone" aria-hidden="true">i</div>
        <div><h2>Aucune location</h2><p>Le bilan apparaîtra dès la première location enregistrée.</p></div>
      </div>
      <div class="actions"><a class="bt" href="ajouterlocation.html">Nouvelle location</a></div>
    </div>
<?php else: ?>
    <dl class="stats">
      <div><dt>Revenu total</dt><dd><?= dt($totalRevenu) ?></dd></div>
      <div><dt>Locations</dt><dd><?= (int)$totalLocations ?></dd></div>
      <div><dt>Voitures louées</dt><dd><?= count($lignes) ?></dd></div>
    </dl>
    <div class="tableau-wrap">
      <table>
        <thead>
          <tr>
            <th>Immatriculation</th>
            <th>Modèle</th>
            <th class="num">Locations</th>
            <th class="num">Jours</th>
            <th class="num">Revenu</th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($lignes as $t): ?>
          <tr>
            <td class="immat"><?= htmlspecialchars($t['immat']) ?></td>
            <td><?= htmlspecialchars($t['typevoiture']) ?></td>
            <td class="num"><?= (int)$t['nb'] ?></td>
            <td class="num"><?= (int)$t['jours'] ?></td>
            <td class="num"><?= dt($t['revenu']) ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2">Total</td>
            <td class="num"><?= (int)$totalLocations ?></td>
            <td class="num"></td>
            <td class="num"><?= dt($totalRevenu) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
<?php endif; ?>
  </div>
</body>
</html>
