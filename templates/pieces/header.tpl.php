<!DOCTYPE html>
<html lang="FR">
<?php if(isset($_GET['connect'])) { unset($_SESSION['arrAuthentification']);}  ?>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" /> 
	<link rel="icon" href="images/favicon.png">
	<link rel="stylesheet" href="stylesheets/app.css" />
	<title><?php echo $this->title; ?></title>
</head>
<body>
<a href="#main" class="visuallyhidden focusable">Aller au contenu</a>
<div class="sidebar">
	<?php include "nav.tpl.php"; ?>
</div>
<header>
	<div class="row">
		<?php include "nav.tpl.php"; ?>
		<div class="subnav">
			<button type="button" lang="en" class="subnav-icon langue"><span class="visuallyhidden">English</span></button>
			<a href="panier.php" class="subnav-icon panier"><span class="visuallyhidden">Consulter le panier d'achat</span></a>
		</div>
	</div>
	<div class="row">
		<div class="recherche right">
			<span class="indicateurDeProgres" style="display:none;"> 
			<!-- http://www.ajaxload.info/ --> 
			<img src="images/ajax-loader.gif" alt="Processus en cours..."> </span>
			<form action="#" method="get">
				<select name="categorie" id="categorie">
					<option value="sujet">Sujet</option>
					<option value="auteur">Auteur</option>
					<option value="titre">Titre</option>
					<option value="isbn">Isbn</option>
				</select>
				<label for="motsCles" class="visuallyhidden">Mots Clés</label>
				<input type="text" name="motsCles" autocomplete="off" id="motsCles">
				<button type="submit"><span class="visuallyhidden">Lancer la recherche</span></button>
			</form>
		</div>
	</div>
	<div class="row achat">
		<span class="indicateurDeProgres" style="display:none;"> 
		<!-- http://www.ajaxload.info/ --> 
		<img src="images/ajax-loader.gif" alt="Processus en cours..."> </span>
	<?php if(isset($this->addedToCart)){ ?>
		<div class="large-6 medium-12 columns">
			<p class="border"><?php echo $this->nombreItemsPanier; ?> article<?php if($this->nombreItemsPanier>0){?>s<?php }?> dans le panier</p>
			<p>Sous-total: <?php echo formatToMoneyType($this->sousTotal); ?></p>
		</div>
		<div class="large-6 medium-12 columns align">
			<a href="panier.php" class="border">Panier</a>
			<a href="ssl/connexion.php">Passer la commande</a>
		</div>
	<?php } ?>
	</div>
	<?php if(isset($this->filArianne) && $this->filArianne){ ?>
	<div class="row arianne">
		<ul>
		<?php foreach ($this->filArianne as $strNom => $strLien) { ?>
			<li><a href="<?php echo $strLien; ?>"><?php echo $strNom; ?></a></li>
		<?php } ?>
		</ul>
	</div>
	<?php } ?>
	<?php if(isset($_SESSION['arrAuthentification'])){ ?>
	<a href="index.php?connect=false" class="connect">Se déconnecter</a>
	<?php }
	else { ?>
	<a href="#" class="connect">Se connecter</a>
	<?php } ?>
</header>
