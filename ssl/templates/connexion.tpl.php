<?php echo $this->entete; ?>
<main id="main" role="main" class="connexion row">
	<div class="large-6 small-12 large-centered small-centered columns">
		<h1>Connexion</h1>
<?php $courriel = isset($_POST['courriel']) ? $_POST['courriel'] : "";
if(!$this->valide){
	echo '<small class="error">'.$this->messageErreurGen.'</small>';
}?>
		<form action="connexion.php" method="POST" class="large-12 small-12 columns">
			<label for="courriel">Votre courriel</label>
			<input type="text" name="courriel" id="courriel" value="<?php echo $courriel; ?>"/>
			<label for="password">Mot de passe</label>
			<input type="password" name="password" id="password" />
			<button type="submit" name="connexion" value="connexion">Connexion</button>
		</form>
		<form action="connexion.php" class="large-12 columns">
			<button type-"submit" name="nouveau" value="submit">Nouveau client</button>
		</form>
		<a href="#" class="large-12 columns">Acheter sans créer de compte</a>
	</div>
</main>
<?php echo $this->piedDePage; ?>