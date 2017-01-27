<?php
require_once("inc/init.inc.php");


$title="Index Front";
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg;

?>

        <div class="row">
		
            <div class="col-md-12">
			
			
			<div class="texteAccueil">
				<h3 class="lokisalle">PRESENTATION</h3>
			</div>
			
			<p class="texteAccueil">Créé en 2016, <strong class="lokisalle">LOKISALLE</strong> vous propose un large choix de salles de réunion et 
			de conférence pouvant accueillir de 10 à 400 personnes dans toute la France et plus précisément à Paris, Lyon et Marseille.<br /><br />
			Nous disposons de petites salles pour travailler avec vos collaborateurs et vos fournisseurs ou pour recevoir vos clients, mais 
			également des salles de conférence pour vos meetings. <strong class="lokisalle">LOKISALLE</strong> met tout en œuvre pour vous 
			faciliter l'organisation de vos réunions.<br /></p>

			<p class="texteAccueil">Toutes les salles proposées disposent de toutes les commodités pour la réussite de vos meetings. 
			Que ce soit pour une réunion d'une heure comme pour un séminaire d'une journée voire plus, les salles de réunion <strong class="lokisalle">LOKISALLE</strong>, vous propose gratuitement la présence d'une hôtesse qui accueillera tous les participants pour les aiguiller vers la salle que vous avez réservée. 
			Elle sera à votre service pour préparer des petits déjeuners, sandwichs ou plateaux-repas, ou encore réserver un restaurant ou un taxi. 
			<strong class="lokisalle">LOKISALLE</strong> met tout en œuvre pour vous simplifier la vie et concourir à la réussite de vos réunions. 
			N’hésitez pas à nous solliciter.<br /></p><hr />

			</div>
		</div>

        <div class="row">
		
		<?php

		// Requête à traiter
		$resultat = req("SELECT s.id_salle, p.id_produit, s.photo, s.titre, p.prix, s.description, s.ville, p.date_arrivee, p.date_depart
				FROM salle s, produit p
				WHERE p.id_salle = s.id_salle
				ORDER BY p.date_arrivee
				LIMIT 0,3");
		
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
			echo '<a href="fiche_produit.php?salle=' . $ligne['id_salle'] . '&produit=' . $ligne['id_produit'] . '"><img src="'. RACINE_SITE . 'photo/' . $ligne['photo'] . '" alt=""></a>';

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
			
			echo '</div></div></div>';
			
			//Affichage
		} // fermeture du while
		}
		else { $msg .= '<p style="color: white; background-color: green; padding: 10px;">Aucune salle n\'est disponible avec les options sélectionnées</p>'; 
	}
		echo '</div>';



// Les pop-up pour l'inscription et la connexion

require_once("formulaires.php");
require_once("inc/bas.front.inc.php");

 

 ?>
 