<?php 
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>, Hugues Raymond-Lessard <hugues.ray@hotmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 2014-11-05
 * Classe de gestion de panier d'achat
 */

class PanierAchat{
	private $arrItems = array();
	private $numFraisDeBase = 0;
	private $numFraisUnitaire = 0;
	private $numTaxes = 0;
	private $strDelais = "";

	/**
	 * @desc 	Cette fonction ajoute un item à l'objet Panier d'Achat. 
	 * 			NOTE: La gestion de la $_SESSION est externe à la classe! 
	 * @param 	Le isbn d'un livre
	 * @param 	L'objet de connexion à la DB
	 */
	function ajouterAuPanier($strIsbnItem, $objConnexion){
		if(isset($this->arrItems[$strIsbnItem])){
			$this->arrItems[$strIsbnItem]['qty'] += 1;
		}else{
			$strRequeteInfosLivre = "
				SELECT titre, prix
				FROM t_livre
				WHERE t_livre.isbn = '$strIsbnItem'";
			$objRequeteInfosLivre = $objConnexion->query($strRequeteInfosLivre);
			$objInfosLivre = $objRequeteInfosLivre->fetch_object();

			$this->arrItems[$strIsbnItem]['titre'] = $objInfosLivre->titre;
			$this->arrItems[$strIsbnItem]['qty'] = 1;
			$this->arrItems[$strIsbnItem]['prix'] = $objInfosLivre->prix;

			$objRequeteInfosLivre->free_result();
		}
	}

	/**
	 * @desc 	Cette fonction ajuste la quantité d'un item dans l'objet Panier d'Achat
	 * @param 	Le isbn d'un livre
	 * @param 	La nouvelle quantité de cet item
	 */
	function ajusterQuantite($strIsbnItem, $intQuantity){
		if(isset($this->arrItems[$strIsbnItem]) && preg_match("#^[0-9]+$#", $intQuantity)){
			if($intQuantity!=0){
				$this->arrItems[$strIsbnItem]['qty'] = $intQuantity;
			}else{
				$this->retirerItem($strIsbnItem);
			}
		}
	}

	/**
	 * @desc 	Cette fonction trouve un item par son ISBN dans l'objet Panier et le retire
	 * @param 	Le ISBN d'un livre
	 */
	function retirerItem($strIsbnItem){
		if(isset($this->arrItems[$strIsbnItem])){
			unset($this->arrItems[$strIsbnItem]);
		}
	}

	/**
	 * @desc 	Getter qui retourne le contenu du panier
	 * @return 	Array des items du panier d'achat
	 */
	function getPanier(){
		return $this->arrItems;
	}

	/**
	 * @desc 	Getter qui retourne la quantité total de livres dans le panier
	 * @return 	Integer du nombre de livres dans le panier
	 */
	function getNombreLivres(){
		$intNombreLivres = 0;
		foreach ($this->arrItems as $donnees) {
			$intNombreLivres += $donnees['qty'];
		}

		return $intNombreLivres;
	}

	/**
	 * @desc 	Getter qui retourne le sous-total du panier
	 * @return 	Number du prix (sous-total)
	 */
	function getSousTotal(){
		$numSousTotal = 0;
		foreach ($this->arrItems as $donnees) {
			$numSousTotal += $donnees['qty']*$donnees['prix'];
		}

		return $numSousTotal;
	}

	/**
	 * @desc 	Getter qui retourne les Frais de livraison
	 * @return 	Number des frais de livraisons
	 */
	function getFraisLivraison(){
		$numFraisDeLivraison = $this->numFraisDeBase;
		foreach ($this->arrItems as $donnees) {
			$numFraisDeLivraison += $donnees['qty']*$this->numFraisUnitaire; 
		}

		return $numFraisDeLivraison;
	}

	/**
	 * @desc 	Getter qui retourne les Taxes
	 * @return 	Number des taxes applicables sur le prix
	 */
	function getTaxes(){
		return $this->getSousTotal()*$this->numTaxes/100;
	}

	
	/**
	 * @desc 	Getter qui retourne le coût total du panier
	 * @return 	Number du prix total
	 */
	function getTotal(){
		$numTotal = $this->getSousTotal() + $this->getTaxes() + $this->getFraisLivraison();

		return $numTotal;
	}

	/**
	 * @desc 	Getter qui retourne le délais de livraison
	 * @return 	String du délais estimé de livraison
	 */
	function getDelais(){
		return $this->strDelais;
	}

	/**
	 * @desc 	Fonction qui récupère les taux de la BD selon le cas sélectionner
	 * @param 	String du nom de taux a utilliser
	 * @param 	Objet MySQLi de connexion
	 */
	function calculerTaux($nomTaux, $objConnexion){
		switch ($nomTaux) {
			case 'standard':
				$strColFraisBase = "mode_standard_base";
				$strColFraisUnitaire = "mode_standard_par_item";
				$strColDelais = "mode_standard_delai";
				break;
			case 'express':
				$strColFraisBase = "mode_prioritaire_base";
				$strColFraisUnitaire = "mode_prioritaire_par_item";
				$strColDelais = "mode_prioritaire_delai";
				break;
			default:
				$strColFraisBase = "mode_standard_base";
				$strColFraisUnitaire = "mode_standard_par_item";
				$strColDelais = "mode_standard_delai";
				break;
		}

		$strRequeteTaux = "SELECT tps, $strColFraisBase, $strColFraisUnitaire, $strColDelais
						   FROM  t_taux
						   ORDER BY id_taux DESC
						   LIMIT 0,1";
		$objRequeteTaux = $objConnexion->query($strRequeteTaux);

		while($objResults = $objRequeteTaux->fetch_object()){
			$this->numTaxes = $objResults->tps;
			$this->numFraisUnitaire = $objResults->$strColFraisUnitaire;
			$this->numFraisDeBase = $objResults->$strColFraisBase;
			$this->strDelais = $objResults->$strColDelais;
		}
	}
}