<?php echo $this->entete; ?>
<main id="main" role="main" class="confirmation row">
	<div class="large-6 small-12 large-centered small-centered columns">
		<h1>Confirmation</h1>
		<div class="sommaire">
			<table>
				<tr>
					<th colspan="2">Sommaire de la commande</th>
				</tr>
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
			</table>
			<p>Livraison à <?php echo $this->arrInfosLivraison['nom']; ?></p>
			<p>Votre numéro de confirmation: <?php echo formaterNoConfirmation($this->noConfirmation); ?></p>
			<p>Adresse: <?php echo $this->arrInfosLivraison['adresse']; ?>, <?php echo $this->arrInfosLivraison['code_postal']; ?>, <?php echo $this->arrInfosLivraison['ville']; ?>, <?php echo $this->arrInfosLivraison['nom_province']; ?>, <?php echo $this->arrInfosLivraison['nom_pays']; ?></p>
			<p>Estimé de livraison: <?php echo $this->delais; ?></p>
		</div>
		<div class="transac">
			<h3>Merci d'avoir acheté chez nous!</h3>
			<a href="../index.php">&lt; Retour à l'accueil</a>
		</div>
		
	</div>
</main>
<?php echo $this->piedDePage; ?>