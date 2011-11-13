DROP TABLE IF EXISTS utilisateurs;


CREATE TABLE utilisateurs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire de la table utilisateurs',
	login VARCHAR(50)  NOT NULL COMMENT 'Login de l\'utilisateur',
	mdp VARCHAR(100) default '' NOT NULL COMMENT 'Mot de passe',
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='utilisateurs';

#-----------------------------------------------------------------------------
#-- joueurs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS joueurs;


CREATE TABLE joueurs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire de la table',
    equipe_id INTEGER(11) NOT NULL COMMENT 'cle etrangere de son equipe',
	nom VARCHAR(50)  NOT NULL COMMENT 'Nom du joueur',
	prenom VARCHAR(50)  NOT NULL COMMENT 'Prenom du joueur',
	civilite VARCHAR(5)  NOT NULL COMMENT 'Civilite',
	naissance INTEGER(12)  NOT NULL COMMENT 'Date de naissance en timestamp UNIX',
	poste INTEGER(1) NOT NULL COMMENT 'poste du joueur',
	numero INTEGER(3) NOT NULL COMMENT 'numero du joueur',
	created_on INTEGER(15) default 0 NOT NULL COMMENT 'Première création',
	updated_on INTEGER(15) default 0 NOT NULL COMMENT 'Dernière mise à jour',
	PRIMARY KEY (id),
	INDEX joueurs_FI_1 (equipe_id),
	CONSTRAINT joueurs_FK_1
		FOREIGN KEY (equipe_id)
		REFERENCES equipes (id)
) ENGINE=MyISAM COMMENT='joueurs';

DROP TABLE IF EXISTS equipes;

CREATE TABLE equipes
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire de la table',
	nom VARCHAR(50)  NOT NULL COMMENT 'Nom de cette equipe',
	adresse VARCHAR(255)  NOT NULL COMMENT 'Adresse',
	ville VARCHAR(50)  NOT NULL COMMENT 'Ville',
	cp INTEGER(6)  NOT NULL COMMENT 'code postal',
	niveau CHAR(5) NOT NULL COMMENT 'niveau de cette equipe',
	cree_le INTEGER(15) default 0 NOT NULL COMMENT 'Première création',
	modifie_le INTEGER(15) default 0 NOT NULL COMMENT 'Dernière mise à jour',
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Liste des équipes';

DROP TABLE IF EXISTS matchs;

CREATE TABLE matchs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire de la table',
    equipe1_id INTEGER(11) NOT NULL COMMENT 'cle etrangere de equipe 1',
    equipe2_id INTEGER(11) NOT NULL COMMENT 'cle etrangere de equipe 2',
    score_1 INTEGER(11) NOT NULL COMMENT 'score equipe 1',
    score_2 INTEGER(11) NOT NULL COMMENT 'score equipe 2',
    code VARCHAR(50)  NOT NULL COMMENT 'Code',
	ville VARCHAR(50)  NOT NULL COMMENT 'Ville',
	niveau CHAR(5) NOT NULL COMMENT 'niveau de ce match',
	date INTEGER(15) default 0 NOT NULL COMMENT 'date',
    cree_le INTEGER(15) default 0 NOT NULL COMMENT 'Première création',
	modifie_le INTEGER(15) default 0 NOT NULL COMMENT 'Dernière mise à jour',
	PRIMARY KEY (id),
    CONSTRAINT matchs_FK_1
		FOREIGN KEY (equipe1_id)
		REFERENCES equipes (id),
	CONSTRAINT matchs_FK_2
		FOREIGN KEY (equipe2_id)
		REFERENCES equipes (id)
) ENGINE=MyISAM COMMENT='Liste des équipes';

DROP TABLE IF EXISTS actions;

CREATE TABLE actions
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire de la table',
    match_id INTEGER(11) NOT NULL COMMENT 'cle etrangere du match',
    joueur_id INTEGER(11) NOT NULL COMMENT 'cle etrangere du joueur',
    type VARCHAR(50) NOT NULL COMMENT 'type d\'action',
    valeur INTEGER(1) NOT NULL COMMENT 'valeur de cette action',
	periode INTEGER(1)  NOT NULL COMMENT 'periode de cette action',
    cree_le INTEGER(15) default 0 NOT NULL COMMENT 'Première création',
	modifie_le INTEGER(15) default 0 NOT NULL COMMENT 'Dernière mise à jour',
	PRIMARY KEY (id),
    CONSTRAINT actions_FK_1
		FOREIGN KEY (match_id)
		REFERENCES matchs (id),
	CONSTRAINT actions_FK_2
		FOREIGN KEY (joueur_id)
		REFERENCES joueurs (id)
) ENGINE=MyISAM COMMENT='Liste des équipes';