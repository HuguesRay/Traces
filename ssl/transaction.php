<?php 
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>, Hugues Raymond-Lessard <hugues.ray@hotmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 2014-10-09
 * 
 */

$strNiveau="";
$intEtape = 3;

// Instancier, configurer et afficher le template
require_once($strNiveau . 'inc/lib/Savant3.class.php');
require_once($strNiveau . 'inc/scripts/config.inc.php');
include_once($strNiveau . 'inc/scripts/utils.inc.php');
include_once($strNiveau . 'inc/lib/PanierAchat.class.php');
include_once($strNiveau . 'inc/scripts/securiteInitPanier.inc.php');
require_once($strNiveau . 'inc/lib/Transaction.class.php');

$arrConfigTpl = array(
  'template_path' => $strNiveau.'templates'
);

$objTpl = new Savant3($arrConfigTpl);

$objTpl->niveau = $strNiveau;
$objTpl->etape = $intEtape;

$arrPanier = $objPanier->getPanier();

//Code qui s'assure que le client est bel et bien authentifié
$blnAuthentification = true;
if(!isset($_SESSION['arrAuthentification'])){
	$blnAuthentification = false;
}else{
	$arrAuthentification = unserialize($_SESSION['arrAuthentification']);
	if(!isset($arrAuthentification['id_client'])){
		$blnAuthentification = false;
	}else{
		$id_client = $arrAuthentification['id_client'];
	}
}

if($blnAuthentification == false){
	header('Location: connexion.php');
	exit();
}


//Fonction d'update du panier d'achat
if(isset($_POST['livraison'])){
	$strLivraison = $_POST['livraison'];
	$_SESSION['livraison'] = $_POST['livraison'];
}else{
	$strLivraison = isset($_SESSION['livraison']) ? $_SESSION['livraison'] : "standard";
}
$objTpl->livraison = $strLivraison;
$objPanier->calculerTaux($strLivraison, $objConnMySQLi);

foreach ($arrPanier as $strIsbn => $arrData) {
	if(isset($_POST['qty'.$strIsbn])){
		$objPanier->ajusterQuantite($strIsbn, $_POST['qty'.$strIsbn]);
	}
}
$_SESSION['objPanier'] = serialize($objPanier);


//Traitement de la transaction

$objTransaction = isset($_SESSION['objTransaction']) ? unserialize($_SESSION['objTransaction']) : new Transaction();

if($blnTransactionInitiee = $objTransaction->initierTransaction($arrAuthentification, $objConnMySQLi, "utillisateur_connecte")){
	$objTransaction->setInfosFacturation('livraison', $strLivraison);

	$objTpl->arrInfosClient = $objTransaction->getInfosClient();
	$objTpl->arrInfosLivraison = $objTransaction->getInfosLivraison();
	$objTpl->arrInfosFacturation = $objTransaction->getInfosFacturation();
}

if(isset($_POST['commande'])){
	if($objTransaction->passerCommande($arrPanier, $objConnMySQLi) != -1){
		$_SESSION['objTransaction'] = serialize($objTransaction);
		header("Location: confirmation.php");
		exit();
	}
}

$_SESSION['objTransaction'] = serialize($objTransaction);


//Fonction de supression d'item du panier
if (isset($_POST['supprimer'])) {
	if(isset($arrPanier[$_POST['supprimer']])){
		$objPanier->retirerItem($_POST['supprimer']);
	}
	$_SESSION['objPanier'] = serialize($objPanier);
	$objTpl->supprimer = true;
}

//Assigner des données comme attributs du template

$objTpl->panierAchat = $objPanier->getPanier();
$objTpl->taxes = $objPanier->getTaxes();
$objTpl->fraisDeLivraison = $objPanier->getFraisLivraison();
$objTpl->total = $objPanier->getTotal();
$objTpl->nombreItemsPanier = $objPanier->getNombreLivres();
$objTpl->sousTotal = $objPanier->getSousTotal();
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

//Assigner des données comme attributs du template
$objTpl->title = "Traces: Validation";

// Définir les composantes de la page avec la méthode fetch

$objTpl->entete=$objTpl->fetch('templates/pieces/header_transac.tpl.php');
$objTpl->piedDePage=$objTpl->fetch('pieces/footer_transac.tpl.php');
// Afficher le template principal
if($blnTransactionInitiee && $objTpl->nombreItemsPanier>0 ){
	$objTpl->display('validation.tpl.php');
}else{
	$objTpl->display('validation_error.tpl.php');
}
$objTpl->close();

