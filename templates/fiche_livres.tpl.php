<?php 
include_once('inc/scripts/utils.inc.php');
echo $this->entete; 
?>

<?php $livre = $this->infosLivre->fetch_object(); ?>

<main id="main" role="main" class="ficheLivres row">
	<div class="small-12 column">
		<div class="row">
			<div class="headerFicheLivre small-12 large-6 large-offset-3 columns">
				<h1><?php echo formaterTitre($livre->titre); ?><?php if($livre->sous_titre!=""){ ?>
					<span class="sous-titre"><?php echo $livre->sous_titre; ?></span><?php } ?>
				</h1>
				<div class="large-10 large-centered columns">
					<img src="<?php echo $this->imgSrc; ?>" alt="" />
					<p><?php echo formatToMoneyType($livre->prix); ?></p>
					<form method="post" action="#">
						<button type="submit" name="ajouter" value="<?php echo $livre->isbn;?>" id="addtocart" class="large-centered">Ajouter<span class="visuallyhidden"> ce titre</span> au panier</button>
					</form>
				</div>
				
			</div>
			<div class="description small-12 large-6 large-offset-3 columns">
				<h2>Description</h2>
				<p><?php echo traiterDescription($livre->description); ?></p>
			</div>
			<div class="auteurs small-12 large-3 columns">
				<h2 class="visuallyhidden">Auteur<?php if(count($this->auteurs)>1){ echo "s"; } ?></h2>
				<?php $arrAuteurs = listerNomsAuteurs($this->auteurs);
						foreach($arrAuteurs as $auteur) {
							echo '<p>'.$auteur.'</p>';
							} ?>
				<a href="#nom">Laissez votre avis</a>
			</div>
			
			<div class="ficheTechnique small-12 large-3 columns">
				<h2 class="visuallyhidden">Fiche technique</h2>
				<p><span class="element">ISBN: </span> <?php echo $livre->isbn;?></p>
				<p><span class="element">Nombre de Pages: </span> <?php echo $livre->nbre_pages;?></p>
				<p><span class="element">Éditeur: <?php if(count($this->editeurs)>1){ echo "s"; } ?></span><?php 
				echo formatterListe(listerNomsEditeurs($this->editeurs)); ?>
					</p>
				<p><span class="element">Année d'édition: </span> <?php echo $livre->annee_publication; ?></p>
				<p><span class="element">Langue: </span> <?php echo $livre->langue;?></p>
			</div>
			<?php if(count($this->honneurs)>0){ ?>
			<div class="prixRemportes small-12 large-6 large-offset-3 columns">
				<h2>Prix Remporté<?php if(count($this->honneurs)>1){ echo "s"; } ?></h2>
			<?php foreach ($this->honneurs as $honneur) { ?>
				<h4 class="titrePrix"><?php echo $honneur->nomPrix; ?></h4>
				<p><?php echo traiterDescription($honneur->descriptionPrix); ?></p>
			<?php } ?>
			</div>
			<?php } ?>
			<?php if(count($this->rescensions)>0){ ?>
			<div class="faitParler small-12 large-6 large-offset-3 columns">
				<h2>Ce livre fait parler de lui</h2>
			<?php foreach ($this->rescensions as $rescention) { ?>
				<h4><?php echo $rescention->titreRescension; ?></h4>
				<p><?php echo formatterDate($rescention->date); ?></p>
				<p><?php echo $rescention->nom_journaliste; ?>, <?php echo $rescention->nom_media; ?></p>
				<p><?php echo traiterDescription($rescention->descriptionRescension); ?></p>
			<?php } ?> 
			</div>
			<?php } ?>
			<div class="comment small-12 large-6 large-offset-3 columns end">
				<h2>Laissez votre avis</h2>
				<form method="post" action="index.php">
					<label for="nom">Votre nom:<input type="text" id="nom" /></label>
					<label for="commentaire">Votre commentaire:<textarea rows="5" id="commentaire"></textarea></label>
					<input type="radio" name="vote" class="visuallyhidden" id="up">
					<label for="up" class="large-2 medium-2 columns"><span class="visuallyhidden">vote plus</span>255</label>
					<input type="radio" name="vote" class="visuallyhidden" id="down">
					<label for="down" class="large-2 medium-2 columns"><span class="visuallyhidden">vote moins</span>25</label>
					<input type="submit" value="Envoyer" class="large-4 right">
				</form>
			</div>
		</div>
	</div>
</main>
<?php echo $this->piedDePage; ?>
