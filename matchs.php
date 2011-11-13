<?php
session_start();

if (!isset ($_SESSION['login'])){
  header("Location:login.php");
}

include 'connect.inc.php';
//$code = isset($_GET['code']) ? $_GET['code'] : NULL;
$ajouter  = isset($_POST['ajouter']) ? $_POST['ajouter'] : NULL;
$contenu  = '';

// Code métier
if ($ajouter == 'Ajouter'){
  // on ajoute l'équipe
  $insert = $cnx->exec("INSERT INTO equipes SET nom = '".$_POST['nom']."', adresse = '".$_POST['adresse']."', ville = '".$_POST['ville']."', cp = '".$_POST['cp']."', niveau = '".$_POST['niveau']."', cree_le = '".date("U")."', modifie_le = '".date("U")."'");
  $equipe_id = $cnx->lastInsertId();
  echo $equipe_id;
  for($a = 0 ; $a < 10; ++$a){
    // On ajoute chaque joueur
    if ($_POST['nomj'][$a] != '' && $_POST['numero'][$a] != ''){
      $add = $cnx->exec("INSERT INTO joueurs SET equipe_id = '".$equipe_id."', nom = '".$_POST['nomj'][$a]."', prenom = '".$_POST['prenomj'][$a]."', civilite = 'M', naissance = '".$_POST['naissance'][$a]."', poste = '".$_POST['poste'][$a]."', numero = '".$_POST['numero'][$a]."', cree_le = '".date("U")."', modifie_le = '".date("U")."'");
      if ($add === false){
        echo '<pre>';
        print_r($cnx->errorInfo());
        echo '</pre>';
      }
    }
    echo $add;
  }
} elseif ($ajouter == 'Match') {
  $test = explode('/', $_POST['date']);
  $date = mktime($test[3], $test[4], 0, $test[1], $test[0], $test[2]);
  $insert = $cnx->exec("INSERT INTO matchs SET equipe1_id = '".$_POST['e1']."', equipe2_id = '".$_POST['e2']."', score_1 = 0, score_2 = 0, code = '".$_POST['code']."', ville = '".$_POST['ville']."', niveau = '".$_POST['niveau']."', date = '".$date."', cree_le = '".date('U')."', modifie_le = '".date('U')."'");
  /*echo '<pre>';
        print_r($_POST);
        echo '</pre>';*/
}
$liste_equipes = $cnx->query("SELECT * FROM equipes");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>
      S.M.B.
    </title>
        <script type="text/javascript" src="prototype.js.pack"></script>
        <script type="text/javascript" src="fonctions.js"></script>
        <link href="style.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <h4><a href="index.php">Retour</a></h4>
    <h2>Gérer les rencontres</h2>
    <div class="droite">
      <p>Liste des équipes - <span class="lien" onclick="changerDisplay('addTeam');">Ajouter</span></p>
      <div id="addTeam" style="display: none;">
        <form action="matchs.php" method="post">
          <p><label for="nom">Nom</label>
            <input id="nom" type="text" name="nom" value="" /></p>
          <p><label for="adresse">Adresse</label>
            <input id="adresse" type="text" name="adresse" value="" /></p>
          <p><label for="ville">Ville</label>
            <input id="ville" type="text" name="ville" value="" /></p>
          <p><label for="cp">Code postal</label>
            <input id="cp" type="text" name="cp" value="" /></p>
          <p><label for="niveau">Niveau</label>
            <input id="niveau" type="text" name="niveau" value="" /></p>
          <table class="table">
            <tr>
              <th title="Pour l'identifier sur le terrain">Numéro</th>
              <th>Nom</th>
              <th>Prénom</th>
              <th title="Date de naissance ou numéro de licence...">Date</th>
              <th>Poste</th>
            </tr>
          <?php // On prévoit 10 joueurs maximum à saisir pour cette équipe
          for($a = 0 ; $a < 10 ; ++$a){
            echo '
            <tr>
              <td><input type="text" name="numero[]" value="" /></td>
              <td><input type="text" name="nomj[]" value="" /></td>
              <td><input type="text" name="prenomj[]" value="" /></td>
              <td><input type="text" name="naissance[]" value="" /></td>
              <td><input type="text" name="poste[]" value="2" /></td>
            </tr>
              ';
          }
          ?>
          </table>
          <p><input type="submit" name="ajouter" value="Ajouter" /></p>
        </form>
      </div>
      <?php
      $option = '';
      foreach ($liste_equipes as $equipe) {
        $option .= '<option value="'.$equipe['id'].'">'.$equipe['nom'].'</option>'."\n";
        $joueurs = $cnx->query("SELECT * FROM joueurs WHERE equipe_id = '".$equipe['id']."' ORDER BY numero");
        echo '<br /><span class="lien" onclick="changerDisplay(\'joueurs'.$equipe['id'].'\');">' . $equipe['nom'] . '</span>';
        echo '<div style="display:none;" id="joueurs'.$equipe['id'].'"><table class="table">';
        foreach ($joueurs as $joueur) {
          echo '
            <tr>
              <td>'.$joueur['numero'].'</td>
              <td>'.$joueur['nom'].'</td>
              <td>'.$joueur['prenom'].'</td>
            </tr>';
        }
        echo "\n".'</table></div>'."\n";
      }
      ?>
    </div>
    <div class="gauche">
      <p class="lien" onclick="changerDisplay('addmatch');">Ajouter un match</p>
      <div style="display: none;" id="addmatch">
        <form action="matchs.php" method="post">
          <p>
            <label for="e1">Equipe 1</label>
            <select name="e1" id="e1">
              <?php echo $option; ?>
            </select>
          </p>
          <p>
            <label for="e2">Equipe 1</label>
            <select name="e2" id="e2">
              <?php echo $option; ?>
            </select>
          </p>
          <p><label for="code">Code</label>
            <input id="code" type="text" name="code" value="" /></p>
          <p><label for="ville">Ville</label>
            <input id="ville" type="text" name="ville" value="" /></p>
          <p><label for="niveau">Niveau</label>
            <input id="niveau" type="text" name="niveau" value="" /></p>
          <p><label for="date" title="de la forme JJ/MM/AAAA/HH/ii">Date</label>
            <input id="date" type="text" name="date" value="JJ/MM/AAAA/HH/ii" /></p>
          <p><input type="submit" name="ajouter" value="Match" /></p>
        </form>
      </div>
    </div>
    <?php echo $contenu; ?>
  </body>
</html>