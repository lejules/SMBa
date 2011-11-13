<?php
/* 
 * Permet de modifier le score en cas d'erreur
 */
session_start();

if (!isset ($_SESSION['login'])){
  header("Location:login.php");
}

include 'connect.inc.php';
$code     = isset($_POST['code']) ? $_POST['code'] : NULL;
$score_1  = isset($_POST['score_1']) ? $_POST['score_1'] : NULL;
$score_2  = isset($_POST['score_2']) ? $_POST['score_2'] : NULL;

$match = $cnx->query("SELECT * FROM matchs WHERE code = '".$code."' LIMIT 1");
foreach ($match as $m) {
  $_id = $m['id'];
  $cnx->query("UPDATE matchs SET score_1 = '".$score_1."', score_2 = '".$score_2."' WHERE code = '".$code."' LIMIT 1");
  $_SESSION['score'][$m['id']][$m['equipe1_id']] = $score_1;
  $_SESSION['score'][$m['id']][$m['equipe2_id']] = $score_2;
}
echo 'ok';
?>