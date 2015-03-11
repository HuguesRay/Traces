<?php echo $this->entete; ?>
<main id="main" role="main" class="panier row">
	<div class="large-6 large-centered medium-12 medium-centered columns">
		<h1>votre panier</h1>
		<?php if ($this->nombreItemsPanier != 0){ ?>
		<form method="post" action="panier.php">
		<?php foreach ($this->panierAchat as $strIsbn => $arrDonnees): ?>
			<div class="livre row">
				<img src="<?php echo $this->imgSrc[$strIsbn]; ?>" alt="" class="large-4 medium-4 columns">
				<div class="large-8 medium-8 columns">
					<h2><?php echo formaterTitre($arrDonnees['titre']); ?></h2>
					<p><?php  echo formatterListe(listerNomsAuteurs($this->auteurs[$strIsbn]));?></p>
					<p class="prix"><?php echo formatToMoneyType($arrDonnees['prix']); ?></p>
				</div>
				<div class="options large-12 small-12 columns">
					<button type="submit" class="large-4 medium-4 columns" name="supprimer" value="<?php echo $strIsbn; ?>">Supprimer<span class="visuallyhidden"> l'article <?php echo formaterTitre($arrDonnees['titre']); ?></span></button>
					<div class="nombre large-8 medium-8 columns">
						<label class="large-8 medium-8 columns" for="qty<?php echo $strIsbn; ?>">Quantité:</label>
						<select name="qty<?php echo $strIsbn; ?>" class="large-4 medium-4 columns" id="qty<?php echo $strIsbn; ?>">
							<?php $limit = $arrDonnees['qty']>10 ? $arrDonnees['qty'] : 10;
							for($i=0; $i<=$limit; $i++){
								$strSelected = $i==$arrDonnees['qty'] ? "selected=selected " : "";
								echo '<option ' . $strSelected . 'value="' . $i . '">'. $i . '</option>';
							} ?>
						</select>
					</div>
					<p>sous-total: <?php echo formatToMoneyType($arrDonnees['qty']*$arrDonnees['prix']);?> </p>
				</div>
			</div>
			<?php endforeach ?>
			<div class="row sous">
				<p class="large-12 columns">Sous-total: <?php echo formatToMoneyType($this->sousTotal); ?></p>
				<p class="large-12 columns">TPS: <?php echo formatToMoneyType( $this->taxes); ?></p>
				<p class="large-12 columns">Frais livraison</p>
				<select name="livraison" id="livraison" class="large-6 small-6 columns">
					<option <?php if($this->livraison == "standard") echo 'selected = "selected" '; ?>value="standard">Standard</option>
					<option <?php if($this->livraison == "express") echo 'selected = "selected" '; ?>value="express">Express</option>
				</select>
				<label for="livraison" class="large-6 small-6 columns">: <?php echo formatToMoneyType($this->fraisDeLivraison); ?></label>
				<p class="large-12 columns">Date estimée de livraison: <?php echo $this->delais; ?></p>
			</div>
			<div class="row transac">
				<h3 class="large-12 medium-12 columns">Total: <?php echo formatToMoneyType($this->total); ?></h3>
				<button type="submit" name="recalculer" value="recalculer">recalculer</button>
				<button type="submit" name="commander" value="commander">passer la commande</button>
				<a href="index.php" class="">&lt; continuer à magasiner</a>
			</div>
		</form>
		<?php }else{ ?>
		<div class="row transac">
			<p>Le panier d'achat est vide!</p>
			<a href="index.php" class="">&lt; continuer à magasiner</a>
		</div>
		<?php } ?>
	</div>
</main>
<?php echo $this->piedDePage; ?>