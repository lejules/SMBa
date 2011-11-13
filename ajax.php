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
$action = isset($_POST['info']) ? $_POST['info'] : NULL;
$a = explode("_", $action);
$sql = "INSERT INTO actions SET match_id = '".$a[0]."', joueur_id = '".$a[1]."', type = '".$a[2]."', valeur = '".$a[3]."', periode = '".$a[4]."', tempsrestant = '".$a[8]."', cree_le = '".date("U")."'";
$query = $cnx->exec($sql);
// On enregistre le score dans la base et en session
$points = array('t1'=>1, 't2'=>2, 't3'=>3);
if (array_key_exists($a[2], $points) === true AND $a[3] == 1){
  $_SESSION['score'][$a[0]][$a[6]] = $_SESSION['score'][$a[0]][$a[6]] + $points[$a[2]];
  $score_e1 = current($_SESSION['score'][$a[0]]);
  $score_e2 = next($_SESSION['score'][$a[0]]);
  $cnx->query("UPDATE matchs SET score_1 = '".$score_e1."', score_2 = '".$score_e2."' WHERE id = '".$a[0]."' LIMIT 1");
}else{
  $score_e1 = current($_SESSION['score'][$a[0]]);
  $score_e2 = next($_SESSION['score'][$a[0]]);
}
// On gère maintenant l'affichage
echo '<span class="red">score : ' . $score_e1 . ' - ' . $score_e2 . '</span> (Qt ' . $a[4] . ')';
?>
