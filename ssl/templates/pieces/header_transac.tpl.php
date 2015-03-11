<!DOCTYPE html>
<html lang="FR">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" /> 
	<link rel="stylesheet" href="stylesheets/app.css" />
	<link rel="icon" href="images/favicon.png">
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
			<button type="button" lang="en" class='subnav-icon langue'><span class="visuallyhidden">English</span></button>
		</div>
	</div>
	<?php if(isset($this->etape)) { ?>
	<div class="row etape">
		<ul>
			<li <?php echo $string = $this->etape == 1 ? 'class=active' : ''; ?>>1. Livraison</li>
			<li <?php echo $string = $this->etape == 2 ? 'class=active' : ''; ?>>2. Facturation</li>
			<li <?php echo $string = $this->etape == 3 ? 'class=active' : ''; ?>>3. Validation</li>
		</ul>
	</div>
	<?php } ?>
</header>
