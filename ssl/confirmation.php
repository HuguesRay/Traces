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
require_once($strNiveau . 'inc/lib/Transaction.class.php');

$arrConfigTpl = array(
  'template_path' => $strNiveau.'templates'
);

$objTpl = new Savant3($arrConfigTpl);

$objTpl->niveau = $strNiveau;

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

$blnConfirmation = true;

//Traitement de la transaction

if(isset($_SESSION['objTransaction'])){
	$objTransaction = unserialize($_SESSION['objTransaction']);

	$objTpl->arrInfosClient = $objTransaction->getInfosClient();
	$objTpl->arrInfosLivraison = $objTransaction->getInfosLivraison();
	$objTpl->arrInfosFacturation = $objTransaction->getInfosFacturation();
}else{
	$blnConfirmation = false;
}

//Assigner des données comme attributs du template
if($blnConfirmation){
	$objTpl->panierAchat = $objPanier->getPanier();
	$objTpl->taxes = $objPanier->getTaxes();
	$objTpl->fraisDeLivraison = $objPanier->getFraisLivraison();
	$objTpl->total = $objPanier->getTotal();
	$objTpl->nombreItemsPanier = $objPanier->getNombreLivres();
	$objTpl->sousTotal = $objPanier->getSousTotal();
	$objTpl->delais = $objPanier->getDelais();
	$objTpl->noConfirmation = $objTransaction->getNoConfirmation();
	
	unset($_SESSION['objTransaction']);
	unset($_SESSION['objPanier']);
}

//Assigner des données comme attributs du template
$objTpl->title = "Traces: Confirmation";

// Définir les composantes de la page avec la méthode fetch

$objTpl->entete=$objTpl->fetch('templates/pieces/header_transac.tpl.php');
$objTpl->piedDePage=$objTpl->fetch('pieces/footer_transac.tpl.php');
// Afficher le template principal
if($blnConfirmation){
	$objTpl->display('confirmation.tpl.php');
}else{
	$objTpl->display('confirmation_error.tpl.php');
}
$objTpl->close();

