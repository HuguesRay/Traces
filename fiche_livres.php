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

//Ajout au panier
if(isset($_POST['ajouter'])){
	$objPanier->ajouterAuPanier($_POST['ajouter'], $objConnMySQLi);
	$_SESSION['objPanier'] = serialize($objPanier);
	$objTpl->addedToCart = true;
}

//Assigner des données comme attributs du template

$isbn = isset($_GET['isbn']) ? $_GET['isbn'] : "";
$valide=validerIsbn($isbn);

// Sera remplacé plus tard si jamais le livre est trouvé
$objTpl->title = "Traces: Fiche de livre introuvable";

if($valide){
	
	$strRequeteTitreLivre=
		"SELECT titre
		 FROM t_livre
		 WHERE isbn = '$isbn'";

	$strRequeteFicheLivre=
		"SELECT nbre_pages, annee_publication, langue, prix, titre, sous_titre, mots_cles, isbn, description, autres_caracteristiques
		FROM t_livre
		WHERE isbn = '$isbn'";

	$strRequeteAuteurs=
		"SELECT a.nom AS nomAuteur
		FROM t_auteur AS a
		INNER JOIN ti_auteur_livre AS b
			ON a.id_auteur = b.id_auteur
		INNER JOIN t_livre AS c
			ON b.id_livre = c.id_livre
		WHERE c.isbn = '$isbn'";

	$strRequeteEditeurs=
		"SELECT a.nom AS nomEditeur, url
		FROM t_editeur AS a
		INNER JOIN ti_editeur_livre AS b
			ON a.id_editeur = b.id_editeur
		INNER JOIN t_livre AS c
			ON b.id_livre = c.id_livre
		WHERE c.isbn = '$isbn'";

	$strRequetePrix=
		"SELECT a.nom AS nomPrix, a.description AS descriptionPrix
		FROM t_honneur AS a
		INNER JOIN ti_honneur_livre AS b
			ON a.id_honneur = b.id_honneur
		INNER JOIN t_livre AS c
			ON b.id_livre = c.id_livre
		WHERE c.isbn = '$isbn'";

	$strRequeteFaitParler=
		"SELECT date, a.titre AS titreRescension, nom_media, nom_journaliste, a.description AS descriptionRescension
		FROM t_recension AS a
		INNER JOIN t_livre AS b
			ON a.id_livre = b.id_livre
		WHERE b.isbn = '$isbn'";

	$objTpl->imgSrc = file_exists("images/covers/L".ISBNToEAN($isbn).".jpg") ? "images/covers/L".ISBNToEAN($isbn).".jpg" : "images/covers/no_preview.jpg" ;
	$objTpl->infosLivre = $objConnMySQLi->query($strRequeteFicheLivre);

	$objQueryTitre = $objConnMySQLi->query($strRequeteTitreLivre);
	$objTitreLivre = $objQueryTitre->fetch_object();

	$objTpl->nomPage = formaterTitre($objTitreLivre->titre);
	$objTpl->title = "Traces: $objTpl->nomPage";
	$objTpl->linkPage = "fiche_livres.php?isbn=$isbn";

	$auteurs = $objConnMySQLi->query($strRequeteAuteurs);
	$objTpl->auteurs = array();
	while($auteur = $auteurs->fetch_object()){
		$objTpl->auteurs[] = $auteur;
	}

	$editeurs = $objConnMySQLi->query($strRequeteEditeurs);
	$objTpl->editeurs = array();
	while($editeur = $editeurs->fetch_object()){
		$objTpl->editeurs[] = $editeur;
	}

	$honneurs = $objConnMySQLi->query($strRequetePrix);
	$objTpl->honneurs = array();
	while($honneur = $honneurs->fetch_object()){
		$objTpl->honneurs[] = $honneur;
	}

	$rescensions = $objConnMySQLi->query($strRequeteFaitParler);
	$objTpl->rescensions = array();
	while($rescension = $rescensions->fetch_object()){
		$objTpl->rescensions[] = $rescension;
	}

	$objTpl->filArianne = true;
}else{
	$valide = false;
}



// Définir les composantes de la page avec la méthode fetch
require_once($strNiveau . 'inc/pieces/header.php');

$objTpl->entete=$objTpl->fetch('templates/pieces/header.tpl.php');
$objTpl->piedDePage=$objTpl->fetch('templates/pieces/footer.tpl.php');

// Afficher le template principal
if(!$valide){
	$objTpl->display('fiche_livres_error.tpl.php');
}else{
	$objTpl->display('fiche_livres.tpl.php');
}
$objConnMySQLi->close();