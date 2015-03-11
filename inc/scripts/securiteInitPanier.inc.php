<?php
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 10 nov. 2014
 * Gère l'initialisation ou récupération des données du panier d'achat sur l'ensemble du site
 */

if(!isset($objPanier)){
	if(isset($_SESSION['objPanier'])){
		$objPanier = unserialize($_SESSION['objPanier']);
	}else{
		$objPanier = new PanierAchat();
	}
}