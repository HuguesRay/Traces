<?php 
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 10 nov. 2014
 * Ce fichier gère l'ajout au pannier fait par un appel Ajax
 */
$strNiveau = "../../";
require_once($strNiveau . 'inc/scripts/config.inc.php');
include_once($strNiveau . 'inc/scripts/utils.inc.php');
include_once($strNiveau . 'inc/lib/PanierAchat.class.php');
include_once($strNiveau . 'inc/scripts/securiteInitPanier.inc.php');

//Ajout au panier
if(isset($_GET['ajouter'])){
	$objPanier->ajouterAuPanier($_GET['ajouter'], $objConnMySQLi);
	$_SESSION['objPanier'] = serialize($objPanier);

	$nombreItemsPanier = $objPanier->getNombreLivres();
	$sousTotal = $objPanier->getSousTotal();
	?>
	<span class="indicateurDeProgres" style="display:none;"> 
			<!-- http://www.ajaxload.info/ --> 
		<img src="images/ajax-loader.gif" alt="Processus en cours..."> </span>
	<div class="large-6 medium-12 columns">
		<p class="border"><?php echo $nombreItemsPanier; ?> article<?php if($nombreItemsPanier>0){?>s<?php }?> dans le panier</p>
		<p>Sous-total: <?php echo formatToMoneyType($sousTotal); ?></p>
	</div>
	<div class="large-6 medium-12 columns align">
		<a href="panier.php" class="border">Panier</a>
		<a href="ssl/connexion.php">Passer la commande</a>
	</div>
<?php } ?>