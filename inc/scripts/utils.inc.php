<?php
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>, Hugues Raymond-Lessard <hugues.ray@hotmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 14-10-21
 * Le fichier de configuration s'occupe de:
 * - Contenir les fonctions utilitaires utilisées par la majorité des pages du site, elles servent majoritairement a classer, ordonner et afficher les données de façon intelligible.
 *
 * NOTE SPÉCIALE AU COURS DE DÉVELOPPEMENT MULTIMÉDIA:
 * Ce fichier de configuration a été créé dans le cadre du projet Traces, développé lors du cours de Production.
 */

/**
 * @param Array contenant 1 ou plusieurs objets d'auteurs
 * @return Array de strings de noms d'auteurs
 * @desc Cette fonction liste en array les strings des noms des auteurs. Elle tolère les alias "nom" et "nomAuteur" utillisé. Si le nom est en deux parties séparé d'une virgule, il sera inversé et la virgule sera retiré (ex: "Bérubé, Roger" => "Roger Bérubé")
 */
function listerNomsAuteurs($arrObjAuteurs){
	$arrListe = array();
	foreach($arrObjAuteurs as $objAuteur){
		if(isset($objAuteur->nomAuteur)){
			$arrListe[] = $objAuteur->nomAuteur;
		}
		if(isset($objAuteur->nom)){
			$arrListe[] = $objAuteur->nom;
		}
	}

	$nbItems = count($arrListe);

	for($i=0; $i<$nbItems; $i++){
		// Teste si le nom de l'auteur est bien fractionné en seulement 2 morceaux
		$arrPieces = explode(', ', $arrListe[$i]);
		if(count($arrPieces) == 2){ 
			$arrListe[$i] = $arrPieces[1] . " " . $arrPieces[0];
		}
	}
	return $arrListe;
}

/**
 * @param Array contenant des objets avec les informations des éditeurs
 * @return Array de Strings de noms d'éditeurs
 * @desc Cette fonction crée une liste avec les noms des éditeurs et leur place une balise lien si définie
 */
function listerNomsEditeurs($arrObjEditeurs){
	$arrListe = array();
	foreach ($arrObjEditeurs as $objEditeur) {
		if(isset($objEditeur->nomEditeur)){
			$nomEditeur = $objEditeur->nomEditeur;
		}
		if(isset($objEditeur->nom)){
			$nomEditeur = $objEditeur->nom;
		}

		if(isset($nomEditeur)){
			$arrListe[] = isset($objEditeur->url) != "" ? "<a href=\"$objEditeur->url\">$nomEditeur</a>" : $nomEditeur;
		}
	}

	return $arrListe;
}

/**
 * @param Array contenant 1 ou plusieurs élément String
 * @return String d'une liste formatée avec des virgules au besoin et d'un "et"
 */
function formatterListe($arrListe){
	$strListeFormatee = "";
	$nbItems = count($arrListe);
	for($i=0; $i<$nbItems; $i++){
		if($i!=0){ // Si ce n'est pas le premier
			if($i+1 == $nbItems){ // Si c'est le dernier
				$strListeFormatee .= " et ";
			}
			else
			{
				$strListeFormatee .= ", ";
			}
		}
		$strListeFormatee .= $arrListe[$i];
	}
	return $strListeFormatee;
} 

/**
 * @param String d'un titre
 * @return String d'un titre formaté
 * @desc Cette fonction cherche pour des éléments de préfixes déplacés et mis entre parenthèse et les replace au début du titre.
 *
 * @dev Cette fonction peut être amélioré; dans son état actuelle elle ne vérifie pas si la '(' ne serait pas partie intégrante du titre. 
 */
function formaterTitre($strTitre){
	$intIndexParenthese = strpos($strTitre, '(');
	if($intIndexParenthese > 0){ // Si le titre contient au moins une parenthèse
		$strExtra = strstr($strTitre, "(" );
		$nbCharsExtra = count($strExtra);

		$strTitre = str_replace($strExtra, "", $strTitre);
		$strExtra = substr($strExtra, 1, $nbCharsExtra-2);

		$strTitreFormatee = "$strExtra $strTitre";
	} else { // Si le titre ne possède pas de parenthèse
		$strTitreFormatee = $strTitre;
	}

	return $strTitreFormatee;
}

/**
 * @param Reçoit une date au format UNIX
 * @return Retourne la date formatée NOM_JOUR NO_JOUR NOM_MOIS NO_ANNÉE
 */
function formatterDate($date){
	setlocale(LC_ALL, "fr_CA");
	$timezone = new DateTimeZone("America/Montreal");
	$dateFormatee = new DateTime($date, $timezone);
	$jourSem = retournerSemaine($dateFormatee->format("w"));
	$jourNum = $dateFormatee->format("d");
	$mois = retournerMois($dateFormatee->format("n"));
	$annee = $dateFormatee->format("Y");

	$strFormatDate = "$jourSem $jourNum $mois $annee";
	return $strFormatDate;
}

/**
 * @param Reçoit un numéro de jour de la semaine
 * @return String du nom du jour de la semaine
 */
function retournerSemaine($noSemaine){
	$noSemaine = intval($noSemaine);
	$semaine = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");

	return $semaine[$noSemaine];
}

/**
 * @param Reçoit un numéro de mois
 * @return String du nom du mois
 */
function retournerMois($noMois){
	$noMois = intval($noMois);
	$mois = array('Invalide', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

	return $mois[$noMois];
}

/**
 * @param String d'un texte de description
 * @return String d'un texte de description formaté
 * @desc Fonction de traitement des descriptions générales.
 */
function traiterDescription($strDescription, $type='full'){
	switch ($type) {
		case 'full':
			$strTraitee = strip_tags($strDescription);
			break;
		case 'cut':
			$maxWords = 120;
			$strDescription = strip_tags($strDescription);
			$phrase_array = explode(' ', $strDescription);
			if(count($phrase_array) > $maxWords) {
				$strTraitee = implode(' ', array_slice($phrase_array, 0, $maxWords)).'...';
			}
			else {
				$strTraitee = implode(' ', $phrase_array);
			}
			break;
		default:
			$strTraitee = $strDescription;
			break;
	}
	
	return $strTraitee;
}

/**
 * @param un ISBN a valider
 * @return Boolean; true si valide, false sinon
 * @desc Cette fonction valide si le isbn est d'un format valide
 */
function validerIsbn($isbn){

	if(preg_match("#^[0-9]-[0-9]{5}-[0-9]{3}-[x0-9]$#", $isbn)){
		$valide = true;
	}else{
		$valide = false;
	}

	return $valide;
}

/**
 * @method ISBNToEAN
 * @desc Convertit un ISBN en format EAN
 * @param string ISBN    Le ISBN à convertir
 * @return string         Le ISBN converti en EAN, ou FALSE si erreur dans le format ou la conversion
 */
function ISBNToEAN ($pIsbn)
{
    $myFirstPart = $mySecondPart = $myEan = $myTotal = "";
    if ($pIsbn == "")
       return false;
    $pIsbn = str_replace("-", "", $pIsbn);
    // ISBN-10
    if (strlen($pIsbn) == 10)
    {
        $myEan = "978" . substr($pIsbn, 0, 9);
        $myFirstPart = intval(substr($myEan, 1, 1)) + intval(substr($myEan, 3, 1)) + intval(substr($myEan, 5, 1)) + intval(substr($myEan, 7, 1)) + intval(substr($myEan, 9, 1)) + intval(substr($myEan, 11, 1));
        $mySecondPart = intval(substr($myEan, 0, 1)) + intval(substr($myEan, 2, 1)) + intval(substr($myEan, 4, 1)) + intval(substr($myEan, 6, 1)) + intval(substr($myEan, 8, 1)) + intval(substr($myEan, 10, 1));
        $tmp = intval(substr((3 * $myFirstPart + $mySecondPart), -1));
        $myControl = ($tmp == 0) ? 0 : 10 - $tmp;
    
        return $myEan . $myControl . 1;
    }
    // ISBN-13
    else if (strlen($pIsbn) == 13) {
    	$pIsbn .= 1;
    	return $pIsbn;
    }
    // Autre
    else return false;
}

/**
 * @param Une chaine de caractère contenant un chiffre
 * @return String du prix converti au format xx,xx$
 */
function formatToMoneyType($strPrixPreliminaire){

	$arrPrix = explode(".", $strPrixPreliminaire);
	$arrPrixAlt = explode(",", $strPrixPreliminaire);
	if(count($arrPrix)==2){
		if(strlen($arrPrix[1])<2){
			$arrPrix[1] .= 0;
		}else if(strlen($arrPrix[1])>2){
			$arrPrix[1] = substr($arrPrix[1], 0, 2);
		}

		$strPrixConverti = $arrPrix[0] . "," . $arrPrix[1] . "$";
	}else if(count($arrPrixAlt)==2){
		if(strlen($arrPrixAlt[1])<2){
			$arrPrixAlt[1] .= 0;
		}else if(strlen($arrPrixAlt[1])>2){
			$arrPrixAlt[1] = substr($arrPrixAlt[1], 0, 2);
		}

		$strPrixConverti = $arrPrixAlt[0] . "," . $arrPrixAlt[1] . "$";
	}else{
		$strPrixConverti = $strPrixPreliminaire.",00$";
	}
	return $strPrixConverti;
}

/**
 * @param 	String du no de la Carte de crédit
 * @param 	Boolean, si le numéro doit apparaître partiellement masqué ou non
 * @return 	String du numéro formatée correctement
 */
function formaterNoCarte($strNoCarte, $blnMasque){
	if($blnMasque){
		$strNoFormate = "XXXX XXXX XXXX ";
	}else{
		$strNoFormate = substr($strNoCarte, 0, 4);
		$strNoFormate .= " ";
		$strNoFormate .= substr($strNoCarte, 4, 4);
		$strNoFormate .= " ";
		$strNoFormate .= substr($strNoCarte, 8, 4);
		$strNoFormate .= " ";		
	}
	$strNoFormate .= substr($strNoCarte, 8, 4);

	return $strNoFormate;
}

/**
 * @param 	String du no de tel
 * @param 	Boolean, si le numéro doit apparaître partiellement masqué ou non
 * @return 	String du numéro formatée correctement
 */
function formaterNoTel($strNoTel, $blnMasque){
	if($blnMasque){
		$strNoFormate = "(XXX) XXX-";
	}else{
		$strNoFormate = '(' . substr($strNoTel, 0, 3) . ') ';
		$strNoFormate .= substr($strNoTel, 3, 3) . '-';
	}
	$strNoFormate .= substr($strNoTel, 6, 4);

	return $strNoFormate;
}

/**
 * @param 	String du no de confirmation
 * @return 	String du numéro formatée correctement
 */
function formaterNoConfirmation($strNoConfirmation){
	while(strlen($strNoConfirmation)<10){
		$strNoConfirmation = "0$strNoConfirmation";
	}
	return $strNoConfirmation;
}