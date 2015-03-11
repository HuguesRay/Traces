<?php echo $this->entete; ?>
<main id="main" role="main" class="accueil row">
<h1 class="visuallyhidden">Accueil</h1>
	<div class="small-12 columns">
		<div class="row coeurs">
			<?php 
			$nbCoeur = 1;
			foreach($this->coups as $coeurs) { ?>
			<?php
			echo '<a class="large-3 large-offset-6 medium-6 medium-offset-3 columns" href="fiche_livres.php?isbn='.$coeurs->isbn.'"><img src="'.$coeurs->imgPath.'" alt="Consulter la fiche du livre ' . formaterTitre($coeurs->titre) . '"></a>';
			?>
			<div class="large-6 medium-12 columns infoCoup">
				<p>Les coups de coeur:</p>
				<h2><?php echo formaterTitre($coeurs->titre); ?></h2>
				<?php echo formatterListe(listerNomsAuteurs($this->auteurCoeur[$coeurs->isbn]));?>
			</div>
			<?php
			$nbCoeur++;
			 } ?>
			<div class="large-2 medium-2 columns controle">
				<button class="gauche"><span class="visuallyhidden">Coup de coeur précédent</span></button>
				<button class="droite"><span class="visuallyhidden">Coup de coeur suivant</span></button>
			</div>
		</div>
		<div class="row content-wrap">
			<div class="actualite large-7 large-offset-5 columns">
				<h2>Actualités littéraires<span class="underline"></span></h2>
				<?php 
				foreach($this->actualite as $actualites) { ?>
					<div class="row">
						<p class="large-12 columns titre"><?php echo $actualites->titre; ?></p>
						<p class="large-6 columns auteur"><?php echo formatterListe(listerNomsAuteurs(array($actualites))); ?></p>
						<p class="large-6 columns date"><?php echo formatterDate($actualites->date); ?></p>
					</div>
					
					<p><?php echo traiterDescription($actualites->texte, 'cut'); ?></p>
					<a href="#" class="more">Lire l'article complet<span class="visuallyhidden"> sur "<?php echo $actualites->titre; ?>"</span></a>
				<?php
				 } ?>
			</div>
		
			<div class="nouveaute large-5 columns">
				<h1>Les nouveautés <br><span>De la semaine</span><span class="underline"></span></h1>
				<?php 
				foreach($this->livre as $livre) { ?>
				<div class="nouveau">
					<p><?php echo formatterListe(listerNomsAuteurs($this->auteur[$livre->isbn])); ?></p>
					<p class="titre"><?php echo formaterTitre($livre->titre); ?></p>
					<?php 
					echo '<a href="fiche_livres.php?isbn='.$livre->isbn.'"><img src="' . $livre->imgPath . '" alt="Consulter la fiche du livre ' . formaterTitre($livre->titre) . '" ></a>';
					?>
				</div>
					
				<?php }
				?>
			</div>
		</div>
		
		
	</div>
</main>
<?php echo $this->piedDePage; ?>