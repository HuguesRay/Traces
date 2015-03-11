<?php 
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 29 oct. 2014
 * Le fichier autocompletion s'occupe de:
 * - analyser les données provenant de la querrystring et adapter les requêtes en conséquences
 * - afficher une liste des résultats de la recherche ou un message d'erreur
*/
include_once('config.inc.php');

//----------- Simulation de délais -----------//
/*$intTotalBoucle = 35;
for($intI = 0; $intI < $intTotalBoucle; $intI++){
	usleep(5000);
}*/


//----------- Récupération du GET -----------//
$strType = isset($_GET['categorie']) ? $_GET['categorie'] : "";
$strMotsCles = isset($_GET['motsCles']) ? $_GET['motsCles'] : "";
$strMotsCles = $objConnMySQLi->real_escape_string($strMotsCles); //Sécurisation des données envoyé dans la requête sql

//----------- Traitement des requêtes -----------//
$strRequete = trouverRequete($strType, $strMotsCles);
if($strRequete!=""){

	$objResultats = $objConnMySQLi->query($strRequete);

	echo traiterRequete($objResultats);

}

$objConnMySQLi->close();


/**
* @param 	String contenant la categorie de recherche
* @param 	String contenant les mots clés de la recherche
* @return 	String contenant la requête SQL appropriée
*/
function trouverRequete($strType, $strMotsCles){
	$strRequete = "";
	switch ($strType) {
	 	case 'auteur':
			$strNomRow = "nom";
			$strNomTable = "t_auteur";
	 		break;
	 	case 'titre':
 			$strNomRow = "titre";
			$strNomTable = "t_livre";
			break;
	}

	if(isset($strNomRow) && isset($strNomTable)){
		$strRequete = "SELECT $strNomRow AS donnee
					FROM $strNomTable
					WHERE $strNomRow LIKE '%$strMotsCles%'
					ORDER BY $strNomRow
					LIMIT 0,5";
	}

	return $strRequete;
}

/**
* @param 	Un objet des résultats retournés par mysqli->query
* @return 	String contenant le html des résultats propice
*/
function traiterRequete($objResultatsRequete){
	$intNbResultats = $objResultatsRequete->num_rows;
	if($intNbResultats>0){
		//La requête retourne des résultats
		$strResultat = '<ul>';
		while($objResultat = $objResultatsRequete->fetch_object()){
			$strResultat .= "<li>$objResultat->donnee</li>";
		}
		$strResultat .= '</ul>';
		$objResultatsRequete->free_result();
	}else{
		//La requête n'a pas retourné de resultats
		$strResultat = "<p class=\"noResults\">Aucun résultat</p>"; 
	}
	return $strResultat;
}