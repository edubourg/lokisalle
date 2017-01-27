<?php
require_once("inc/init.inc.php");
/*$title="Accueil";
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg;*/

// Traitement du formulaire POST
	//debug($_POST);

if ($_POST) {

	
	// Tests de vérification
	// Pour parer aux failles XSS
	foreach ($_POST as $indice => $valeur) { 
		$_POST[$indice] = htmlspecialchars($valeur);
	}

	foreach($_POST as $indice => $valeur){ // Parer à Injection SQL
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
	if (strlen($date_arrivee) !=0 && strlen($date_depart) != 0)
	{
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
		
		// la requête proposée est :
		$etat = "libre";
		$requete = "SELECT s.id_salle, p.id_produit, s.photo, s.titre, p.prix, s.description, s.ville, p.date_arrivee, p.date_depart
				FROM salle s, produit p
				WHERE p.id_salle = s.id_salle
				AND p.date_arrivee BETWEEN '" . $date_en_arrivee . "' AND '" . $date_en_depart . "'
				AND p.date_depart BETWEEN '" . $date_en_arrivee . "' AND '" . $date_en_depart . "'
				AND capacite <= " . $capacite . "
				AND ville = '" . $ville . "'
				AND prix <= " . $prix . "
				AND p.etat = '" . $etat . "'
				ORDER BY p.date_arrivee";
	}
	else
	{
		// requête sans les dates de bornage
		$etat = "libre";
		$requete = "SELECT s.id_salle, p.id_produit, s.photo, s.titre, p.prix, s.description, s.ville, p.date_arrivee, p.date_depart
				FROM salle s, produit p
				WHERE p.id_salle = s.id_salle
				AND capacite <= " . $capacite . "
				AND ville = '" . $ville . "'
				AND prix <= " . $prix . "
				AND p.etat = '" . $etat . "'
				ORDER BY p.date_arrivee";
	}

	if(empty($msg))
		{
		// Requête à traiter
		$resultat = req($requete);
	
		echo '<div class="container">';
		echo '<div class="row">';
		echo '<div class="col-md-9">';

		// Tester si la requête renvoie un résultat$reponse!=false
		if($resultat->num_rows != 0){
			echo '<h2 style="text-align:center;">Résultats</h2>';
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
            echo '<h4 class="pull-left">' . '<a href="fiche_produit.php?salle=' . $ligne['id_salle'] . '&produit=' . $ligne['id_produit'] .'"> ' . $ligne['titre'] . '</a></h4>';
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


