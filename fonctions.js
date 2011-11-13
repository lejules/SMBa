function actionAjax(info){
  var tmpsrestant = $('compteur');
  var minVar = Math.floor(tmpsrestant.value/60);
  var secVar = tmpsrestant.value % 60;
  o_options = new Object();
  o_options = {postBody: 'info='+info+'_'+tmpsrestant.value};
  location.href='#';
  var laRequete = new Ajax.Updater('aff_action','ajax.php',o_options);
  // affichage
  var i = info.split('_');
  var a = new Array('rate', 'réussit');
  var c = new Array();
  c["t1"]='un lancer franc';
  c["t2"]='un panier';
  c["t3"]='un 3 points';
  c["ro"]='un rebond offensif';
  c["rd"]='un rebond défensif';
  c["pd"]='une passe décisive';
  c["int"]='une interception';
  c["bp"]='perd la balle';
  c["f"]='commet une faute';
  c["cgt"]='';
  if (i[2] == 'cgt'){
    a[0] = 'revient sur le banc';
    a[1] = 'entre en jeu';
  }else{
    a[0] = 'rate';
    (i[2] == 'f' || i[2] == 'bp') ? a[1] = '' : a[1] = 'réussit';
  }
  var eqclass = '';
  if(i[7] == 1){
    var tdadd = '';
  }else if(i[7] == 2){
    var tdadd = '></td><td';
  }
  var insert = new Insertion.After('aff_suivitr', '<tr><td'+tdadd+'>'+i[5]+' '+a[i[3]]+' '+c[i[2]]+' ('+minVar+':'+secVar+')</td></tr>');
}
function envoiServeur(url){
  var contenu = $('div_suivi');
  o_options = new Object();
  o_options = {postBody: 'info='+contenu.innerHTML};
  location.href='#';
  var laRequete = new Ajax.Updater('div_maj_serveur',url,o_options);
}
function addCommentaire(){
  var com = $('comm');
  var insert = new Insertion.After('aff_suivi', '<br />'+com.value);
}
function addScore(code, sc1, sc2){
  var score1 = $(sc1).value;
  var score2 = $(sc2).value;
  o_options = new Object();
  o_options = {postBody: 'code='+code+'&score_1='+score1+'&score_2='+score2};
  var laRequete = new Ajax.Request('score.php',o_options);
}
function changerDisplay(id){
  Element.toggle(id);
}