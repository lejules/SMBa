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
$code = isset($_GET['code']) ? $_GET['code'] : NULL;
$per  = isset($_GET['per']) ? $_GET['per'] : NULL;
$type = isset($_GET['type']) ? $_GET['type'] : NULL;
$_id  = 0;
$contenu = '';

// On récupère toutes les données de ce  match
$match = $cnx->query("SELECT * FROM matchs WHERE code = '".$code."'");
foreach ($match as $m) {
  $_id = $m['id'];
  $contenu .= '<p>Match joué à ' . $m['ville'] . ' le ' . date("d/m/Y H:i", $m['date']) . '</p>';
}
// On peut afficher les stats du match ou alors eulement période par période
$contenu .= '
  <p>
    <a href="stats.php?code='.$code.'">Complète</a> -
    <a href="stats.php?code='.$code.'&amp;per=1">Qt 1</a> -
    <a href="stats.php?code='.$code.'&amp;per=2">Qt 2</a> -
    <a href="stats.php?code='.$code.'&amp;per=3">Qt 3</a> -
    <a href="stats.php?code='.$code.'&amp;per=4">Qt 4</a>';
if (!is_null($per)){
  $contenu .= ' - Période ' . $per;
}
$contenu .= '</p>';
// Les données des joueurs
if (is_null($per)){
  $actions = $cnx->query("SELECT * FROM actions a, joueurs j WHERE a.joueur_id = j.id AND match_id = '".$_id."' ORDER BY j.equipe_id, j.numero");
}else{
  $actions = $cnx->query("SELECT * FROM actions a, joueurs j WHERE a.joueur_id = j.id AND match_id = '".$_id."' AND periode = '".$per."' ORDER BY j.equipe_id, j.numero");
}
$id_joueur = 0;
$aff = array();
// les totaux pour la première équipe
$_e_id = current($actions);
$equipe_id = $_e_id['equipe_id'];
$totaux[$equipe_id] = array('1pt'=>0, '2pts'=>0, '3pts'=>0, 't1'=>0, 't2'=>0, 't3'=>0,
                            'ro'=>0, 'rd'=>0, 'pd'=>0, 'bp'=>0, 'int'=>0, 'f'=>0);
$totaux = array();
foreach ($actions as $action) {
  if ($action['type'] == 'tpsmort'){
    continue;
  }
  /**
   * @todo : terminer la gestion du temps de jeu de chaque joueur
   */
  if ($action['type'] == 'cgt'){
    continue;
  }
  if ($action['id'] != $id_joueur){
    $id_joueur = $action['id'];
    $aff[$id_joueur] = array('infos'=>array($action['numero'], $action['nom'], $action['equipe_id']),
                            '1pt'=>0, '2pts'=>0, '3pts'=>0, 't1'=>0, 't2'=>0, 't3'=>0,
                            'ro'=>0, 'rd'=>0, 'pd'=>0, 'bp'=>0, 'int'=>0, 'f'=>0);
    // Les totaux pour la deuxième équipe
    if ($equipe_id != $action['equipe_id']){
      $totaux[$action['equipe_id']] = array('1pt'=>0, '2pts'=>0, '3pts'=>0, 't1'=>0, 't2'=>0, 't3'=>0,
                            'ro'=>0, 'rd'=>0, 'pd'=>0, 'bp'=>0, 'int'=>0, 'f'=>0);
      $equipe_id = $action['equipe_id'];
    }
  }
  // t1 t2 t3 ro rd pd bp int
  $aff[$id_joueur][$action['type']] = $aff[$id_joueur][$action['type']] + 1;
  $totaux[$action['equipe_id']][$action['type']] = $totaux[$action['equipe_id']][$action['type']] + 1;
  if ($action['type'] == 't1' && $action['valeur'] == 1){
    $aff[$id_joueur]['1pt'] = $aff[$id_joueur]['1pt'] + 1;
    $totaux[$action['equipe_id']]['1pt'] = $totaux[$action['equipe_id']]['1pt'] + 1;
  }
  if ($action['type'] == 't2' && $action['valeur'] == 1){
    $aff[$id_joueur]['2pts'] = $aff[$id_joueur]['2pts'] + 1;
    $totaux[$action['equipe_id']]['2pts'] = $totaux[$action['equipe_id']]['2pts'] + 1;
  }
  if ($action['type'] == 't3' && $action['valeur'] == 1){
    $aff[$id_joueur]['3pts'] = $aff[$id_joueur]['3pts'] + 1;
    $totaux[$action['equipe_id']]['3pts'] = $totaux[$action['equipe_id']]['3pts'] + 1;
  }
}
$contenu .= '
  <table class="table">
    <tr>
      <th>Numéro</th>
      <th>Nom</th>
      <th>2pts</th>
      <th>3pts</th>
      <th>LF</th>
      <th>RO</th>
      <th>RD</th>
      <th>PD</th>
      <th>BP</th>
      <th>Int</th>
      <th>Ftes</th>
      <th>Points</th>
    </tr>';
$class = 'i';
$_eq = current($aff);
$equipe_id = $_eq['infos'][2];
$total_pts = 0;
foreach ($aff as $a) {
  if ($equipe_id != $a['infos'][2]){
    $contenu .= '<tr>
      <td></td>
      <td></td>
      <td>' . $totaux[$equipe_id]['2pts'] . '/' . $totaux[$equipe_id]['t2'] . '</td>
      <td>' . $totaux[$equipe_id]['3pts'] . '/' . $totaux[$equipe_id]['t3'] . '</td>
      <td>' . $totaux[$equipe_id]['1pt'] . '/' . $totaux[$equipe_id]['t1'] . '</td>
      <td>' . $totaux[$equipe_id]['ro'] . '</td>
      <td>' . $totaux[$equipe_id]['rd'] . '</td>
      <td>' . $totaux[$equipe_id]['pd'] . '</td>
      <td>' . $totaux[$equipe_id]['bp'] . '</td>
      <td>' . $totaux[$equipe_id]['int'] . '</td>
      <td>' . $totaux[$equipe_id]['f'] . '</td>
      <td>' . $total_pts . '</td>
    </tr>';
    $total_pts = 0;
    $contenu .= '<tr><td colspan="12"></td></tr>';
    $equipe_id = $a['infos'][2];
  }
  $pts = $a['1pt'] + (2 * $a['2pts']) + (3 * $a['3pts']);
  $contenu .= '
    <tr class="'.$class.'">
      <td>' . $a['infos'][0] . '</td>
      <td>' . $a['infos'][1] . '</td>
      <td>' . $a['2pts'] . '/' . $a['t2'] . '</td>
      <td>' . $a['3pts'] . '/' . $a['t3'] . '</td>
      <td>' . $a['1pt'] . '/' . $a['t1'] . '</td>
      <td>' . $a['ro'] . '</td>
      <td>' . $a['rd'] . '</td>
      <td>' . $a['pd'] . '</td>
      <td>' . $a['bp'] . '</td>
      <td>' . $a['int'] . '</td>
      <td>' . $a['f'] . '</td>
      <td>' . $pts . '</td>
    </tr>';
  $total_pts = $total_pts + $pts;
  $class = $class == 'i' ? 'p' : 'i';
}
if (!empty ($aff)){
  $contenu .= '<tr>
      <td></td>
      <td></td>
      <td>' . $totaux[$equipe_id]['2pts'] . '/' . $totaux[$equipe_id]['t2'] . '</td>
      <td>' . $totaux[$equipe_id]['3pts'] . '/' . $totaux[$equipe_id]['t3'] . '</td>
      <td>' . $totaux[$equipe_id]['1pt'] . '/' . $totaux[$equipe_id]['t1'] . '</td>
      <td>' . $totaux[$equipe_id]['ro'] . '</td>
      <td>' . $totaux[$equipe_id]['rd'] . '</td>
      <td>' . $totaux[$equipe_id]['pd'] . '</td>
      <td>' . $totaux[$equipe_id]['bp'] . '</td>
      <td>' . $totaux[$equipe_id]['int'] . '</td>
      <td>' . $totaux[$equipe_id]['f'] . '</td>
      <td>' . $total_pts . '</td>
    </tr>';
}
$contenu .= '</table>';

if ($type == 'pdf'){
  require_once(dirname(__FILE__).'/html2pdf/html2pdf.class.php');
  $html2pdf = new HTML2PDF('P','A4','fr');
  $html2pdf->WriteHTML($contenu);
  $html2pdf->Output('stats_match_'.$code.'.pdf');
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
  </head>
  <body>
    <div style="position: absolute;margin-left: 50%;" id="aff_action"></div>
    <h4><a href="index.php">Retour</a></h4>
    <?php echo $contenu; ?>
  </body>
</html>