<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();

if (!isset ($_SESSION['login'])){
  header("Location:login.php");
}

include 'connect.inc.php';

// Reste à définir le contenu de la page
$code = isset ($_GET['code']) ? $_GET['code'] : NULL;
$p    = isset ($_GET['p']) ? $_GET['p'] : '1';
$contenu = '';

// le match
// Liste des équipes
  $l_equipes = $cnx->query("SELECT * FROM equipes");
  $equipes = array();
  foreach ($l_equipes as $value) {
    $equipes[$value['id']] = array('nom'=>$value['nom'], 'ville'=>$value['ville'], 'niveau'=>$value['niveau']);
  }
if (!is_null($code)){
  $match = $cnx->query("SELECT * FROM matchs WHERE code = '".$code."'");
  $_SESSION['score'] = array();
  $id_equipe = array();
  /*$contenu .= '<!--<div style="position: absolute;margin-left: 50%;" id="div_suivi">
      <p style="text-align: center;" id="aff_action"></p>
      <p class="bouton" id="aff_suivi" onclick="envoiServeur(\'test.php\');">Suivi des actions :</p>
    </div>--!>';*/
  foreach ($match as $value) {
    $_SESSION['score'][$value['id']] = array($value['equipe1_id']=>$value['score_1'], $value['equipe2_id']=>$value['score_2']);
    $id_match = $value['id'];
    $id_equipe[0] = $value['equipe1_id'];
    $id_equipe[1] = $value['equipe2_id'];
    $contenu .= '<p><a href="index.php">RETOUR</a> - ' . $equipes[$value['equipe1_id']]['nom'] . ' contre '.$equipes[$value['equipe2_id']]['nom'].'</p>';
    $contenu .= '<p>' . $value['niveau'] . ' à ' . $value['ville'] . ' 
      <span style="color:red;font-weight:bold;">Période '.$p.'</span>
        - <input name="comm" id="comm" type="text" value="" /><span class="bouton" onclick="addCommentaire();">Ajouter</span>';
  }
  $contenu .= '
        <a href="index.php?code='.$code.'&amp;p=1">QT 1</a> -
        <a href="index.php?code='.$code.'&amp;p=2">QT 2</a> -
        <a href="index.php?code='.$code.'&amp;p=3">QT 3</a> -
        <a href="index.php?code='.$code.'&amp;p=4">QT 4</a> -
        <input type="text" style="width:30px;" name="idscore_1" id="idscore_1" value="" />
        <input type="text" style="width:30px;" name="idscore_2" id="idscore_2" value="" />
        <span class="bouton" title="Modifier le score" onclick="addScore(\''.$code.'\', \'idscore_1\', \'idscore_2\');">Mod.</span>
        <input type="text" name="compteur" id="compteur" size="2" readonly="readonly" />
        <input type="checkbox" name="MaCheck" id="MaCheck" onclick="Relance(this);" checked="checked"/>
      </p>';
  // On récupère la liste des joueurs des deux équipes
  $order = (isset($_GET['order']) AND $_GET['order'] == 'ASC') ? 'ASC' : 'DESC';
  $joueurs = $cnx->query("SELECT * FROM joueurs WHERE equipe_id = '".$id_equipe[0]."' OR equipe_id = '".$id_equipe[1]."' ORDER BY equipe_id ".$order.", numero");
  //$joueurs = $cnx->query("SELECT * FROM joueurs j, matchs m WHERE code = '".$code."' AND equipe_id IN (equipe1_id, equipe2_id) ORDER BY equipe1_id, equipe2_id, equipe_id DESC, numero");
  $contenu .= '<div class="droite"><table class="table">';

  $class = 'i';
  $marqueur = false;
  $e_id = current($joueurs);
  $equipe_id = $e_id['equipe_id'];
  foreach ($joueurs as $joueur) {
    if ($equipe_id != $joueur['equipe_id'] && $marqueur !== false){
      $eq1ou2 = 1;
      $contenu .= '</table></div><div class="gauche"><table class="table">';
      $equipe_id = $joueur['equipe_id'];
    }elseif($equipe_id != $joueur['equipe_id']){
      $eq1ou2 = 2;
      $equipe_id = $joueur['equipe_id'];
    }
    $marqueur = true;
    $contenu .= '<tr class="'.$class.'"><td>' . $joueur['numero'] . '</td><td>' . $joueur['nom'] . '</td><td>'
                . '<span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_cgt_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">E</span>
                  /<span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_cgt_0_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">S</span>
                  </td>
                  <td>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_t2_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">2pts</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_t3_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">3pts</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_t2_0_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">T2</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_t3_0_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">T3</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_t1_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">1pt</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_t1_0_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">LF</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_ro_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">RO</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_rd_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">RD</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_bp_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">BP</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_pd_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">PDc</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_int_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">Int</span>
                  <span title="' . $joueur['numero'] . ' ' . $joueur['nom'] . '" class="bouton" onclick="actionAjax(\'' . $id_match . '_' . $joueur['id'] . '_f_1_' . $p . '_' . str_replace("'", "", $joueur['nom']) . '_' . $joueur['equipe_id'] . '_' . $eq1ou2 . '\');">Faute</span>
                  </td></tr>';
    $class = $class == 'i' ? 'p' : 'i';
  }
  $contenu .= '</table></div>
    <div style="position: absolute;margin-left: 15%;" id="div_suivi">
      <p style="text-align: center;" id="aff_action"></p>
      <p class="bouton" id="aff_suivi" onclick="envoiServeur(\'test.php\');">Suivi des actions :</p>
      <table class="table">
        <tr id="aff_suivitr"><td></td><td></td></tr>
      </table>
    </div>
    <div id="div_maj_serveur">Mise à jour serveur</div>';

}else{
  $contenu .= '<p><a href="matchs.php">Ajouter une rencontre et/ou des joueurs</a></p>';
  $liste = $cnx->query("SELECT * FROM matchs ORDER BY date");
  foreach ($liste as $value) {
    $contenu .= '
      <p>
        <a href="index.php?code='.$value['code'].'">'.$equipes[$value['equipe1_id']]['nom'].'
      contre '.$equipes[$value['equipe2_id']]['nom'].' le '.date("d/m/Y H:i", $value['date']).' à '.$value['ville'].'</a>
        - <a href="stats.php?code='.$value['code'].'">Stats</a>
      </p>';
  }
}


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
        <script type="text/javascript">
	var valeur_origine = 600;


	var valeur = valeur_origine
	var x;
	function Init()	{
		window.document.getElementById('compteur').value=valeur;
		x = window.setInterval('Decompte()', 1000);
	}

	function Decompte()	{
		((valeur > 0)&&( ! window.document.getElementById('MaCheck').checked)) ? (window.document.getElementById('compteur').value = --valeur) : (window.clearInterval(x));
	}

	function Relance(elem)	{
		if( ! elem.checked )		x= window.setInterval('Decompte()', 1000);
	}

	function ResetCompteur()	{
		valeur = valeur_origine;
		window.document.getElementById('MaCheck').checked = false;
		window.clearInterval(x)
		Init();
	}
	window.onload = Init;
</script>
  </head>
  <body>

    <?php echo $contenu; ?>

  </body>
</html>