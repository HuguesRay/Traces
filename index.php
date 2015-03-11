<?php 
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>, Hugues Raymond-Lessard <hugues.ray@hotmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 2014-10-09
 * 
 */

$strNiveau="";

// Instancier, configurer et afficher le template
require_once($strNiveau . 'inc/lib/Savant3.class.php');
require_once($strNiveau . 'inc/scripts/config.inc.php');
include_once($strNiveau . 'inc/scripts/utils.inc.php');
include_once($strNiveau . 'inc/lib/PanierAchat.class.php');
include_once($strNiveau . 'inc/scripts/securiteInitPanier.inc.php');


$arrConfigTpl = array(
  'template_path' => $strNiveau.'templates'
);

$objTpl = new Savant3($arrConfigTpl);

$objTpl->niveau = $strNiveau;

//----------NOUVEAUTÉ------------//
$nouveaute = $objConnMySQLi->query("
	SELECT titre, isbn, prix, id_livre
	FROM t_livre INNER JOIN t_parution 
	ON t_parution.id_parution = t_livre.id_parution 
	WHERE t_parution.etat = 'nouveauté'
	LIMIT 0,5");
$objTpl->livre = array();
$intI = 0;
while($livre=$nouveaute->fetch_object()) {
	$objTpl->livre[$intI]=$livre;
	$objTpl->livre[$intI]->imgPath = file_exists("images/thumbnails/L".ISBNToEAN($livre->isbn).".jpg") ? "images/thumbnails/L" . ISBNToEAN($livre->isbn) . ".jpg" : "images/thumbnails/no_preview.jpg";
	$intI++;
}
//------AUTEURS-NOUVEAUTE--------//
$objTpl->auteur = array();
foreach ($objTpl->livre as $value) {
	$auteurs = $objConnMySQLi->query("
 	SELECT nom
	FROM t_auteur 
	INNER JOIN ti_auteur_livre 
	ON ti_auteur_livre.id_auteur = t_auteur.id_auteur 
 	INNER JOIN t_livre 
 	ON t_livre.id_livre = ti_auteur_livre.id_livre
 	WHERE t_livre.isbn = '".$value->isbn."'");

 	while($auteur=$auteurs->fetch_object()) {
 		$objTpl->auteur[$value->isbn][]=$auteur; //THIS IS WHAT I CALL MAGIC ಠ_ಠ
 	}
}
//-----------ACTUALITES------------//
$nouvelles = $objConnMySQLi->query("
	SELECT date, titre, texte, t_actualite.id_auteur, nom
	FROM t_actualite
	INNER JOIN t_auteur
	ON t_auteur.id_auteur = t_actualite.id_auteur
	ORDER BY date DESC
	LIMIT 0,5");
if( !$nouvelles)
  die($objConnMySQLi->error);

$objTpl->actualite=array();
while($actualite = $nouvelles->fetch_object()) {
	$objTpl->actualite[]=$actualite;
}

//--------COUP DE COEUR-----------//
$coeurs = $objConnMySQLi->query("
	SELECT titre, isbn, id_livre 
	FROM t_livre
	WHERE est_coup_de_coeur=1
	ORDER BY RAND()
	LIMIT 0,1");
if( !$coeurs)
  die($objConnMySQLi->error);

$objTpl->coups=array();
$intI = 0;
while($coeur = $coeurs->fetch_object()) {
	$objTpl->coups[$intI]=$coeur;
	$objTpl->coups[$intI]->imgPath = file_exists("images/thumbnails/L".ISBNToEAN($coeur->isbn).".jpg") ? "images/thumbnails/L" . ISBNToEAN($coeur->isbn) . ".jpg" : "images/thumbnails/no_preview.jpg";
	$intI++;
}
//------AUTEURS COUP DE COEUR------//
$objTpl->auteurCoeur = array();
foreach ($objTpl->coups as $value) {
	$auteursCoeur = $objConnMySQLi->query("
 	SELECT nom
	FROM t_auteur 
	INNER JOIN ti_auteur_livre 
	ON ti_auteur_livre.id_auteur = t_auteur.id_auteur 
 	INNER JOIN t_livre 
 	ON t_livre.id_livre = ti_auteur_livre.id_livre
 	WHERE t_livre.isbn = '".$value->isbn."'");

 	while($auteurCoeur=$auteursCoeur->fetch_object()) {
 		$objTpl->auteurCoeur[$value->isbn][]=$auteurCoeur; //THIS IS WHAT I CALL MAGIC ಠ_ಠ
 	}
}

//Assigner des données comme attributs du template
$objTpl->title = "Traces: Accueil";

// Définir les composantes de la page avec la méthode fetch
require_once($strNiveau . 'inc/pieces/header.php');

$objTpl->entete=$objTpl->fetch('templates/pieces/header.tpl.php');
$objTpl->piedDePage=$objTpl->fetch('pieces/footer.tpl.php');
// Afficher le template principal
$objTpl->display('index.tpl.php');
$objTpl->close();