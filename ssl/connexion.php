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

//Tester la connexion
if(isset($_SESSION['arrAuthentification'])){
	header('Location: transaction.php');
	exit();
}
$blnValide=true;
$strMessage = "";
if(isset($_POST['connexion'])){
	$strCourriel = isset($_POST['courriel']) ? $_POST['courriel'] : "";
	$strMotDePasse = isset($_POST['password']) ? $_POST['password'] : "";
	if(strlen($strCourriel)>0 && strlen($strMotDePasse)>0){
		$regex = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/"; 
		if(preg_match($regex, $strCourriel)) {
			$objRequetePrepare = $objConnMySQLi->prepare("SELECT id_client FROM t_client WHERE courriel_client = ?
															AND mot_passe = ?");
			$objRequetePrepare->bind_param('ss',$strCourriel, $strMotDePasse);

			if($objRequetePrepare->execute()){
				$arrUserLogin = array();
				$objRequetePrepare->store_result();
				$objRequetePrepare->bind_result($id_client);
				

				while($objRow = $objRequetePrepare->fetch()){
					$arrUserLogin=array('id_client' => $id_client);
				}

				if($objRequetePrepare->num_rows()==1) {
					$_SESSION['arrAuthentification'] = serialize($arrUserLogin);
					header("location:transaction.php");
				}
				if($objRequetePrepare->num_rows()==0) {
					$blnValide = false;
					$strMessage = "Le courriel ou le mot de passe ne correspond a aucun utilisateur";
				}
				if($objRequetePrepare->num_rows()>1){
					$blnValide = false;
					$strMessage = "Une erreur est survenue dans la base de données: plusieurs comptes sont associés a ce courriel. Veuillez contacter l'administrateur du site pour corriger ce problème!";
				}

				$objRequetePrepare->free_result();
			}
		}
		else {
			$blnValide = false;
			$strMessage = "Le courriel n'est pas valide";
		}
	}
	else{
		$blnValide = false;
		$strMessage = "Le nom d'utilisateur et le mot de passe sont requis pour se connecter!";
	}
}


$objTpl->valide = $blnValide;
$objTpl->messageErreurGen = $strMessage;
$objTpl->title = "Connexion";


// Définir les composantes de la page avec la méthode fetch

$objTpl->entete=$objTpl->fetch('templates/pieces/header_transac.tpl.php');
$objTpl->piedDePage=$objTpl->fetch('pieces/footer_transac.tpl.php');
// Afficher le template principal
$objTpl->display('connexion.tpl.php');
$objTpl->close();