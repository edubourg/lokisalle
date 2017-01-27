<?php
require_once("inc/init.inc.php");
$title="Accueil";
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg;

// Traitement du formulaire POST

if ($_POST) {
	
	// Récupération des paramètres
	foreach($_POST as $indice => $valeur){
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}
	
	extract($_POST);
	// les données du formulaire sont $categorie $ville $capacite $prix $date_arrivee $date_depart
	// cas 1 : $categorie et $ville 
	
	// tester si $capacite est null
	if(strlen($capacite) == 0 ){
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Attention : Vous devez renseigner la capacité recherchée !</p>'; 
	}
	
	// Tester si les dates sont renseignées
	
	// Transforme date
	$date_en_arrivee = convertDateEn($date_arrivee);
	$date_en_depart = convertDateEn($date_depart);

	// Contrôle des dates
	$date_actuelle = Date("Y-m-d"); // Obtention de la date actuelle
	$ts_date_actuelle = strtotime($date_actuelle);
	$ts_date_en_arrivee = strtotime($date_en_arrivee);
	$ts_date_en_depart = strtotime($date_en_depart);

	if (($ts_date_en_arrivee < $ts_date_actuelle) || ($ts_date_en_depart < $ts_date_actuelle)) {
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Attention ! Recherche sur des dates antérieures !</p>'; 
		
	}
	
	if ($ts_date_en_arrivee > $ts_date_en_depart ) {
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Attention ! La date d\'arrivée est après la date de départ !</p>'; 
		
	}
	
    //echo 'le résultat en français est ' . $date_arrivee . 'en anglais c est ' . $date_en_arrivee;
    //echo 'le résultat en français est ' . $date_depart . 'en anglais c est ' . $date_en_depart;

	if(empty($msg))
		{
	// Requête à traiter
		$resultat = req("SELECT s.id_salle, p.id_produit, s.photo, s.titre, p.prix, s.description, s.ville, p.date_arrivee, p.date_depart
				FROM salle s, produit p
				WHERE p.id_salle = s.id_salle
				AND p.date_arrivee BETWEEN '" . $date_en_arrivee . "' AND '" . $date_en_depart . "'
				AND p.date_depart BETWEEN '" . $date_en_arrivee . "' AND '" . $date_en_depart . "'
				AND capacite <= " . $capacite . "
				AND ville = '" . $ville . "'
				AND prix <= " . $prix . "
				ORDER BY p.date_arrivee");
	
		//echo $requete;
	
		echo '<div class="container">';
		echo '<div class="row">';
		echo '<div class="col-md-12">';

		// Tester si la requête renvoie un résultat$reponse!=false
		if($resultat->num_rows != 0){
			while ($ligne = $resultat -> fetch_assoc()) {

			//Récupération de la note
			$note_avis = calcul_note($ligne['id_salle']);
			
			//Traitement des dates
			$date_arrivee_reformat = date("d/m/Y", strtotime($ligne['date_arrivee'])); 
			$date_depart_reformat = date("d/m/Y", strtotime($ligne['date_depart'])); 

			//Affichage
			echo '<div class="col-sm-4 col-lg-4 col-md-4">';
            echo '<div class="thumbnail">';
			echo '<a href="fiche_produit.php?salle=' . $ligne['id_salle'] . '&produit=' . $ligne['id_produit'] . '"><img src="'. RACINE_SITE . '/photo/' . $ligne['photo'] . '" alt=""></a>';

			echo '<div class="caption">';
            echo '<h4 class="pull-left">' . '<a href="fiche_produit.php?salle=' . $ligne['id_salle'] . '&produit=' . $ligne['id_produit'] .'">Salle ' . $ligne['titre'] . '</a></h4>';
            echo '<h4 class="pull-right">' . $ligne['prix'] . '  €</h4>';
            echo '<p class="pull-left">' . substr($ligne['description'], 0, 30) . '...' . '</p>';
			echo '<p class="pull-left"><span class="glyphicon glyphicon-search"></span> Du ' . $date_arrivee_reformat . ' au ' . $date_depart_reformat . '</p>'; 
			echo '</div>';

			echo '<div class="ratings">';
			echo '<p class="pull-right"><a href="#"><span class="glyphicon glyphicon-zoom-out"></span>&nbsp;Voir</a></p>';
			if ($note_avis!= 0) {

				for ($j = 0 ; $j < $note_avis; $j++) {
					echo '<span class="glyphicon glyphicon-star"></span>';
				}
			}
			else
			{
					echo '<span class="glyphicon glyphicon-arrow-right">&nbsp;Aucun avis</span>';
				
			}
			
			echo '</p></div></div></div>';

			//Affichage
		} // fermeture du while
		}
		else { $msg .= '<p style="color: white; background-color: green; padding: 10px;">Aucune salle n\'est disponible avec les options sélectionnées</p>'; 
	}
	
	echo '</div></div></div>';	
	}
}	



?>

        <div class="row">

			<!-- Préparation pour version 2 de la page avec jquery 
            <div class="col-md-3">
                <p class="lead">Catégorie</p>
                <div class="list-group">
                    <a href="#" class="list-group-item">reunion</a>
                    <a href="#" class="list-group-item">bureau</a>
                    <a href="#" class="list-group-item">formation</a>
                </div>
				
                <p class="lead">Ville</p>
                <div class="list-group">
                    <a href="#" class="list-group-item">Paris</a>
                    <a href="#" class="list-group-item">Lyon</a>
                    <a href="#" class="list-group-item">Marseille</a>
                </div>
				
            </div>-->
			
            <div class="col-md-12">
			
				<div class="form-group">
					<h1>Recherche d’une location de salle pour réservation</h1>
				</div>
				
				<form role="form" action="" method="post">
					
					<div class="col-md-6">

						<div class="form-group">
							<label>Catégorie</label>
							<select class="form-control" name="categorie">
								<option value="reunion" selected>reunion</option>
                                <option value="bureau">bureau</option>
                                <option value="formation">formation</option>
							</select>
						</div>

						<div class="form-group">
							<label>Ville</label>
							<select class="form-control" name="ville">
                                <option value="Paris" selected>Paris</option>
                                <option value="Lyon">Lyon</option>
                                <option value="Marseille">Marseille</option>
							</select>
						</div>
					
						<div class="form-group">
							<label>Capacité<span class="mandatory">*</span></label></label>
							<select class="form-control" name="capacite">
								<?php
								$i= 1;
								while ($i <= 100) {
									echo '<option value="' . $i . '">' . $i . '</option>';
									$i++;
								} ?>
							</select>
						</div>

					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label>Prix<span class="mandatory">*</span></label></label>
							<input type="range" min="0" max="1500" step="5" name="prix" oninput="document.getElementById('AfficheRange').textContent=value" />
							<span id="AfficheRange">0</span>
						</div>
					
						<div class="form-group">
							<label>Date d'arrivée<span class="mandatory">*</span></label></label>
							<input class="form-control" id="datepick" size="20" name="date_arrivee" placeholder="dd-mm-aaaa"/>
						</div>
					
						<div class="form-group">
							<label>Date de départ<span class="mandatory">*</span></label></label>
							<input class="form-control" id="datepick2" size="20" name="date_depart" placeholder="dd-mm-aaaa"/>
						</div>
				
						<div class="form-group">
							<button type="submit" class="btn btn-lg">Enregistrer</button>
							<button type="reset" class="btn btn-default">Remise à zéro</button>
						</div>


					</div>
				</form>				
				
			</div>

		</div>


<?php 


require_once("formulaires.php");
require_once("inc/bas.front.inc.php");

 


 ?>
 