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

$arrPanier = $objPanier->getPanier();

//Fonction d'update du panier d'achat
foreach ($arrPanier as $strIsbn => $arrData) {
	if(isset($_POST['qty'.$strIsbn])){
		$objPanier->ajusterQuantite($strIsbn, $_POST['qty'.$strIsbn]);
	}
}

if(isset($_POST['livraison'])){
	$_SESSION['livraison'] = $_POST['livraison'];
}else{
	$_SESSION['livraison'] = isset($_SESSION['livraison']) ? $_SESSION['livraison'] : 'standard';
}

$objTpl->livraison = $_SESSION['livraison'];

$objPanier->calculerTaux($_SESSION['livraison'], $objConnMySQLi);

$_SESSION['objPanier'] = serialize($objPanier);

//Fonction de navigation à la transaction
if(isset($_POST['commander'])){
	header('Location: ssl/connexion.php');
	exit();
}

//Fonction de supression d'item du panier
if (isset($_POST['supprimer'])) {
	if(isset($arrPanier[$_POST['supprimer']])){
		$objPanier->retirerItem($_POST['supprimer']);
	}
	$_SESSION['objPanier'] = serialize($objPanier);
	$objTpl->supprimer = true;
}

//Assigner des données comme attributs du template
$objTpl->title = "Traces: Votre panier";

$objTpl->panierAchat = $objPanier->getPanier();
$objTpl->taxes = $objPanier->getTaxes();
$objTpl->fraisDeLivraison = $objPanier->getFraisLivraison();
$objTpl->total = $objPanier->getTotal();
$objTpl->delais = $objPanier->getDelais();

$arrAuteursParLivres = array();
$objTpl->imgSrc = array();
foreach ( $arrPanier as $isbn => $arrDonnees) {
	$arrAuteursParLivres[$isbn] = array();
	$strRequeteAuteurs = "
	SELECT nom AS nomAuteur
	FROM t_auteur AS a
	INNER JOIN ti_auteur_livre AS b
		ON a.id_auteur = b.id_auteur
	INNER JOIN t_livre as c
		ON b.id_livre = c.id_livre
	WHERE isbn = '$isbn'";
	$objAuteurQuery = $objConnMySQLi->query($strRequeteAuteurs);
	while($rowResultats = $objAuteurQuery->fetch_object()){
		$arrAuteursParLivres[$isbn][] = $rowResultats;
	}
	$objAuteurQuery->free_result();
	$objTpl->imgSrc[$isbn] = file_exists("images/thumbnails/L".ISBNToEAN($isbn).".jpg") ? "images/covers/L".ISBNToEAN($isbn).".jpg" : "images/covers/no_preview.jpg" ;
}
$objTpl->auteurs = $arrAuteursParLivres;


// Définir les composantes de la page avec la méthode fetch
require_once($strNiveau . 'inc/pieces/header.php');

$objTpl->entete=$objTpl->fetch('templates/pieces/header.tpl.php');
$objTpl->piedDePage=$objTpl->fetch('templates/pieces/footer.tpl.php');

// Afficher le template principal
$objTpl->display('panier.tpl.php');
$objConnMySQLi->close();