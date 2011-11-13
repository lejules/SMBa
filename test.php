<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$debut = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="10">
    <title>
      S.M.B.
    </title>
    <style>
      .red {color:red;font-weight:bold;}
      .table{border: 2px solid blue;}
      .table td{border: 1px solid blue;}
    </style>
  </head>
  <body><h4>Cette page se recharge toute seule toutes les 10 secondes environ !</h4>';
$fin = '</body></html>';
/*Initialisation de la ressource curl*/
/*$c = curl_init();
curl_setopt($c, CURLOPT_URL, 'http://serveur_SMB/index.php');
//On indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//On indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour
curl_setopt($c, CURLOPT_HEADER, false);
//On indique à curl d'envoyer une requete post
curl_setopt($c, CURLOPT_POST,true);
//On donne les paramêtre de la requete post
curl_setopt($c, CURLOPT_POSTFIELDS,array('info'=>$_POST['info'], 'mdp'=>'motdepassedecnx'));
//On execute la requete
$output = curl_exec($c);
//On a une erreur alors on la leve
if($output === false)
{
	trigger_error('Erreur curl : '.curl_error($c),E_USER_WARNING);
}
//Si tout c'est bien passé on affiche le contenu de la requête
else
{
	//var_dump($output);
}
//On ferme la ressource
curl_close($c);*/
// On crée une page avec du contenu
file_put_contents('test.html', $debut.$_POST['info'].$fin);
echo date("d/m/Y H:i:s");
?>
