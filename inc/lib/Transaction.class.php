<?php
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 2014-11-10
 * Classe de gestion de la transaction
 */

class Transaction{
	private $_arrInfosClient = array();
	private $_arrInfosLivraison = array();
	private $_arrInfosFacturation = array();
	private $_noConfirmation = 0;

	/**
	 * @desc 	Cette fonction Initialise la transaction
	 * @param 	Array des données d'authentification
	 * @param 	Objet de connexion MySQLi
	 * @param 	String de cas, si l'usager se connecte, cré un compte ou n'utilise pas de compte (UNUSED)
	 * @return 	Boolean si la transaction a été correctement initialisée
	 */
	function initierTransaction($arrAuthentification, $objConnMySQLi, $strCase){
		$blnTransactionInitialisee = true;
		if($strCase == "utillisateur_connecte"){ //Si l'usager se connecte a un compte
			if(isset($arrAuthentification['id_client'])){ //Si l'authentification a fonctionner
				$this->trouverClient($arrAuthentification['id_client'], $objConnMySQLi);
				$this->trouverAdresseLivraison($arrAuthentification['id_client'], $objConnMySQLi);
				$this->trouverAdresseFacturation($arrAuthentification['id_client'], $objConnMySQLi);
				$this->trouverModeDePaiement($arrAuthentification['id_client'], $objConnMySQLi);
			}else{ //Si l'authentification n'a pas fonctionner
				$blnTransactionInitialisee = false;
			}
		}else{ //Si l'usager ne se connecte pas a un compte 
			$blnTransactionInitialisee = false;
		}

		return $blnTransactionInitialisee;
	}

	/**
	 * @desc 	Trouve un client et ajoute ses informations à l'objet de Transaction
	 * @param 	Int du numéro de id du client
	 * @param 	Objet de connexion MySQLi
	 */
	function trouverClient($id_client, $objConnMySQLi){
		$this->_arrInfosClient['id_client'] = $id_client;
		$strRequeteClient = "SELECT nom_client, courriel_client, telephone_client
							 FROM t_client
							 WHERE id_client = $id_client";
		$objRequeteClient = $objConnMySQLi->query($strRequeteClient);
		while ($rowResultats = $objRequeteClient->fetch_object()) {
			$this->_arrInfosClient['nom'] = $rowResultats->nom_client;
			$this->_arrInfosClient['courriel'] = $rowResultats->courriel_client;
			$this->_arrInfosClient['telephone'] = $rowResultats->telephone_client;
		}
		$objRequeteClient->free_result();
	}

	/**
	 * @desc 	Trouve une adresse de livraison d'un client donné
	 * @param 	Int du numéro de id du client
	 * @param 	Objet de connexion MySQLi
	 */
	function trouverAdresseLivraison($id_client, $objConnMySQLi){
		$strRequeteLivraison = "SELECT id_adresse, nom_complet, adresse, ville, code_postal, nom_province, nom_pays
								FROM t_adresse AS a
								INNER JOIN t_province AS b
									ON a.abbreviation_province = b.abbreviation_province
								INNER JOIN t_pays AS c
									ON b.abbreviation_pays = c.abbreviation_pays
								WHERE id_client = $id_client
									AND type = 'livraison'";
		$objRequeteLivraison = $objConnMySQLi->query($strRequeteLivraison);
		while ($rowResultats = $objRequeteLivraison->fetch_object()) {
			$this->_arrInfosLivraison['id_adresse'] = $rowResultats->id_adresse;
			$this->_arrInfosLivraison['nom'] = $rowResultats->nom_complet;
			$this->_arrInfosLivraison['adresse'] = $rowResultats->adresse;
			$this->_arrInfosLivraison['ville'] = $rowResultats->ville;
			$this->_arrInfosLivraison['code_postal'] = $rowResultats->code_postal;
			$this->_arrInfosLivraison['nom_province'] = $rowResultats->nom_province;
			$this->_arrInfosLivraison['nom_pays'] = $rowResultats->nom_pays;
		}
		$objRequeteLivraison->free_result();
	}

	/**
	 * @desc 	Trouve une adresse de facturation d'un client donné
	 * @param 	Int du numéro de id du client
	 * @param 	Objet de connexion MySQLi
	 */
	function trouverAdresseFacturation($id_client, $objConnMySQLi){
		$strRequeteFacturation = "SELECT id_adresse, nom_complet, adresse, ville, code_postal, nom_province, nom_pays
								FROM t_adresse AS a
								INNER JOIN t_province AS b
									ON a.abbreviation_province = b.abbreviation_province
								INNER JOIN t_pays AS c
									ON b.abbreviation_pays = c.abbreviation_pays
								WHERE id_client = $id_client
									AND type = 'facturation'";
		$objRequeteFacturation = $objConnMySQLi->query($strRequeteFacturation);
		while ($rowResultats = $objRequeteFacturation->fetch_object()) {
			$this->_arrInfosFacturation['id_adresse'] = $rowResultats->id_adresse;
			$this->_arrInfosFacturation['nom'] = $rowResultats->nom_complet;
			$this->_arrInfosFacturation['adresse'] = $rowResultats->adresse;
			$this->_arrInfosFacturation['ville'] = $rowResultats->ville;
			$this->_arrInfosFacturation['code_postal'] = $rowResultats->code_postal;
			$this->_arrInfosFacturation['nom_province'] = $rowResultats->nom_province;
			$this->_arrInfosFacturation['nom_pays'] = $rowResultats->nom_pays;
		}
		$objRequeteFacturation->free_result();
	}

	/**
	 * @desc 	Trouve le mode de paiement d'un client donné
	 * @param 	Int du numéro de id du client
	 * @param 	Objet de connexion MySQLi
	 */
	function trouverModeDePaiement($id_client, $objConnMySQLi){
		$strRequeteModeDePaiement = "SELECT id_mode_paiement, no_carte, type_carte, date_expiration_carte
									 FROM t_mode_paiement
									 WHERE id_client = $id_client
									 	AND est_defaut = 1";
		$objRequeteModeDePaiement = $objConnMySQLi->query($strRequeteModeDePaiement);
		while ($rowResultats = $objRequeteModeDePaiement->fetch_object()) {
			$this->_arrInfosFacturation['id_mode_paiement'] = $rowResultats->id_mode_paiement;
			$this->_arrInfosFacturation['no_carte'] = $rowResultats->no_carte;
			$this->_arrInfosFacturation['type_carte'] = $rowResultats->type_carte;
			$this->_arrInfosFacturation['date_expiration_carte'] = $rowResultats->date_expiration_carte;
		}
		$objRequeteModeDePaiement->free_result();
	}

	/**
	 * @desc 	Fait les entrées de la BD néccéssaire à l'ajout de commande
	 * @param 	Array des objets du panier formattés par la classe PanierAchat
	 * @param 	Objet de connexion MySQLi
	 * @return 	Int du numéro de la commande ajoutée
	 */
	function passerCommande($arrPanier, $objConnMySQLi){
		$strRequeteCommander = "INSERT INTO `t_commande`(`id_commande`, `etat`, `date_commande`, `mode_livraison`, `telephone`, `courriel`, `id_client`, `id_adresse_livraison`, `id_adresse_facturation`, `id_mode_paiement`, `id_taux`) 
		VALUES (NULL,?,?,?,?,?,?,?,?,?,?)";
		$objRequeteCommander = $objConnMySQLi->prepare($strRequeteCommander);
		$strNouvelle = 'Nouvelle';
		$dateNow = date('now');
		$intTaux = 3;

		$objRequeteCommander->bind_param('sssisiiiii', 
			$strNouvelle, 
			$dateNow, 
			$this->_arrInfosFacturation['livraison'],
			$this->_arrInfosClient['telephone'],
			$this->_arrInfosClient['courriel'],
			$this->_arrInfosClient['id_client'],
			$this->_arrInfosLivraison['id_adresse'],
			$this->_arrInfosFacturation['id_adresse'],
			$this->_arrInfosFacturation['id_mode_paiement'],
			$intTaux);
		if($objRequeteCommander->execute()){
			$intIdCommande = $objRequeteCommander->insert_id;
		}
		$objRequeteCommander->free_result();

		if(isset($intIdCommande)){
			foreach ($arrPanier as $isbn => $data) {
				if(count($isbn)!=13){
					$strRequeteAjouterItem = "INSERT INTO `t_ligne_commande`(`id_ligne_commande`, `isbn`, `prix`, `quantite`, `id_commande`) 
					VALUES (NULL,?,?,?,?)";
					$objRequeteAjouterItem = $objConnMySQLi->prepare($strRequeteAjouterItem);
					$objRequeteAjouterItem->bind_param('ssii',
						$isbn,
						$data['prix'],
						$data['qty'],
						$intIdCommande);
					$objRequeteAjouterItem->execute();
				}
			}
		}else{
			$intIdCommande = -1;
		}

		$this->_noConfirmation = $intIdCommande;
		return $intIdCommande;
	}

	/**
	 * @desc 	Getter qui retourne les informations du client
	 * @return 	Array des informations client
	 */
	function getInfosClient(){
		return $this->_arrInfosClient;
	}

	/**
	 * @desc 	Setter qui change ou ajoute une valeur au tableau des informations du client
	 * @param 	String du nom de la cellule a affecter
	 * @param 	String de la valeur de la cellule
	 */
	function setInfosClient($cell, $value){
		$this->_arrInfosClient[$cell] = $value;
	}

	/**
	 * @desc 	Getter qui retourne les informations de livraison
	 * @return 	Array des informations de livraison
	 */
	function getInfosLivraison(){
		return $this->_arrInfosLivraison;
	}

	/**
	 * @desc 	Setter qui change ou ajoute une valeur au tableau des informations de livraison
	 * @param 	String du nom de la cellule a affecter
	 * @param 	String de la valeur de la cellule
	 */
	function setInfosLivraison($cell, $value){
		$this->_arrInfosLivraison[$cell] = $value;
	}
	
	/**
	 * @desc 	Getter qui retourne les informations de facturation
	 * @return 	Array des informations de facturation
	 */
	function getInfosFacturation(){
		return $this->_arrInfosFacturation;
	}

	/**
	 * @desc 	Setter qui change ou ajoute une valeur au tableau des informations de facturation
	 * @param 	String du nom de la cellule a affecter
	 * @param 	String de la valeur de la cellule
	 */
	function setInfosFacturation($cell, $value){
		$this->_arrInfosFacturation[$cell] = $value;
	}

	/**
	 * @desc 	Getter qui retourne le numéro de confirmation
	 * @return 	Int du no de confirmation
	 */
	function getNoConfirmation(){
		return $this->_noConfirmation;
	}
}