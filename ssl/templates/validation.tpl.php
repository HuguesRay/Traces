<?php echo $this->entete; ?>
<main id="main" role="main" class="validation row">
	<div class="large-6 small-12 large-centered small-centered columns">
		<form method="post" action="transaction.php">
			<div class="sommaire">
				<h1>Validation de la commande</h1>
				<button class="commande" type="submit" name="commande" value="validation">passer la commande</button>
				<p>Livraison à <?php echo $this->arrInfosClient['nom'] ?></p>
				<p>date estimée de livraison</p>
				<p><?php echo $this->delais; ?></p>
				<table>
					<thead>
						<tr>
							<th colspan="2">Sommaire de la commande</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $this->nombreItemsPanier; ?> items</td>
							<td><?php echo formatToMoneyType($this->sousTotal); ?></td>
						</tr>
						<tr>
							<td>taxes</td>
							<td><?php echo formatToMoneyType($this->taxes); ?></td>
						</tr>
						<tr>
							<td>Livraison</td>
							<td><?php echo formatToMoneyType($this->fraisDeLivraison); ?></td>
						</tr>
						<tr class="medium">
							<td>Total</td>
							<td><?php echo formatToMoneyType($this->total); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="adresse">
				<h2>Adresse de livraison</h2>
				<p><?php echo $this->arrInfosLivraison['nom']; ?></p>
				<p><?php echo $this->arrInfosLivraison['adresse']; ?>, <?php echo $this->arrInfosLivraison['code_postal']; ?>, <?php echo $this->arrInfosLivraison['ville']; ?>, <?php echo $this->arrInfosLivraison['nom_province']; ?>, <?php echo $this->arrInfosLivraison['nom_pays']; ?></p>

				<button>Éditer<span class="visuallyhidden"> l'adresse de livraison</span></button>
			</div>
			<div class="facturation">
				<h2>informations de facturation</h2>
				<h3>infos carte de crédit</h3>
				<p><?php echo formaterNoCarte($this->arrInfosFacturation['no_carte'], true);?></p>
				<p>Expire le: <?php echo formatterDate($this->arrInfosFacturation['date_expiration_carte']); ?></p>
				<button>Éditer<span class="visuallyhidden"> informations de facturation</span></button>
				<h3>Adresse de facturation</h3>
				<p><?php echo $this->arrInfosFacturation['adresse']; ?>, <?php echo $this->arrInfosFacturation['code_postal']; ?>, <?php echo $this->arrInfosFacturation['ville']; ?>, <?php echo $this->arrInfosFacturation['nom_province']; ?>, <?php echo $this->arrInfosFacturation['nom_pays']; ?></p>
				<button>Éditer<span class="visuallyhidden"> l'adresse de facturation</span></button>
				<h3>Informations</h3>
				<p><?php echo $this->arrInfosClient['courriel']; ?></p>
				<p><?php echo formaterNoTel($this->arrInfosClient['telephone'], true); ?></p>
				<button>Éditer<span class="visuallyhidden"> informations de l'usager</span></button>
			</div>
			<div class="panier">
				<h2>votre panier</h2>
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
			</div>
			<div class="row sous">

				<p class="large-12 columns">Sous-total: <?php echo formatToMoneyType($this->sousTotal); ?></p>
				<p class="large-12 columns">TPS: <?php echo formatToMoneyType( $this->taxes); ?></p>
				<p class="large-12 columns">Frais livraison</p>
				<select name="livraison" id="livraison" class="large-6 small-6 columns">
					<option <?php if($this->livraison == "standard") echo 'selected = "selected" '; ?>value="standard">Standard</option>
					<option <?php if($this->livraison == "express") echo 'selected = "selected" '; ?>value="express">Express</option>
				</select>
				<label for="livraison" class="large-6 small-6 columns">: <?php echo formatToMoneyType($this->fraisDeLivraison); ?></label>
			</div>
			<div class="row transac">
				<h3 class="large-12 medium-12 columns">Total: <?php echo formatToMoneyType($this->total); ?></h3>
				<button class="" type="submit" name="recalculer" value="recalculer">recalculer</button>
				<button class="commande" type="submit" name="commande" value="validation">passer la commande</button>
				<a href="index.php" class="">&lt; continuer à magasiner</a>
			</div>
		</form>
	</div>
</main>
<?php echo $this->piedDePage; ?>