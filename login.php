<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$message = '';

if (isset ($_POST['login'])){
  include 'connect.inc.php';
  // On limite le login à 10 caractères
  $login = substr($_POST['login'], 0, 9);

  // On cherche dans la base si il existe un login de ce type et on envoie
  foreach ($cnx->query("SELECT mdp FROM utilisateurs WHERE login = '".$login."'") as $value) {
    // On vérifie que le mdpo est le bon
    if (md5($_POST['mdp']) == $value['mdp']){
      session_start();
      $_SESSION['login'] = $login;
      $message = '<p><a href="index.php">Aller à la page</a></p>';
    }
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>
      Connexion
    </title>
  </head>
  <body>
    <?php echo $message; ?>
    <form action="login.php" method="post">
    <fieldset>
      <legend>Connexion</legend>
      <label for="login">Identifiant</label>
      <input id="login" type="text" name="login" value="" />
      <label for="mdp">Mot de passe</label>
      <input id="mdp" type="password" name="mdp" value="" />

      <input type="submit" name="envoyer" value="env" />
    </fieldset>
      </form>
  </body>
</html>