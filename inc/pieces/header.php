<?php 
/**
 * @author Renaud Marcoux <remmarcoux@gmail.com>, Hugues Raymond-Lessard <hugues.ray@hotmail.com>
 * @copyright Copyright (c)2014 – Cégep de sainte-Foy
 * Date: 2014-10-09
 * 
 */

$objTpl->nombreItemsPanier = $objPanier->getNombreLivres();
$objTpl->sousTotal = $objPanier->getSousTotal();

$_SESSION['previous'] = isset($_SESSION['active']) ? $_SESSION['active'] : "";
$_SESSION['previousLink'] = isset($_SESSION['activeLink']) ? $_SESSION['activeLink'] : "";

