<?php
require_once("inc/init.inc.php");
$title="Fiche produit";
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg;


if ($_GET) {
	
	// Récupération des paramètres
	foreach ($_POST as $indice => $valeur) { // faille XSS
		$_POST[$indice] = htmlspecialchars($valeur);
	}

	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}
	
	extract($_GET);
	$erreur = 0;
	
	// Premier cas : le numéro de salle et/ou le produit est NULL (effacé par l'internaute)
	if (!empty($salle) && !empty($produit) ) {
		
		// Requête à traiter
		$resultat = req("SELECT s.photo, s.titre, p.prix, s.description, s.adresse, s.cp, s.ville, s.capacite, s.categorie, p.date_arrivee, p.date_depart
				FROM salle s, produit p
				WHERE p.id_salle = s.id_salle
				AND s.id_salle = " . $salle . "
				AND p.id_produit = " . $produit);

		// Deuxième cas : le numéro de salle et/ou le produit est inconnu
		if ($resultat -> num_rows == 0) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;">La salle ' . $salle . ' et/ou le produit '. $produit . ' est inconnu.';	
			$erreur++;
		}		
	}
	else 
	{
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">La salle et/ou le produit manquent.';	
		$erreur++;
	}
	
	if (empty($erreur))
	{
		$ligne = $resultat -> fetch_assoc(); // un seul résultat

		//Récupération de la note
		$note_avis = calcul_note($salle);
		
		$date_arrivee_reformat = date("d/m/Y", strtotime($ligne['date_arrivee'])); 
		$date_depart_reformat = date("d/m/Y", strtotime($ligne['date_depart'])); 
	
		// Nom de la salle et nombre d'étoiles
		echo '<div class="row"><div class="col-lg-12">';
		echo '<h1 class="pull-left">Salle ' . $ligne['titre']. ' ';
		echo '<small>';
		for ($j = 0 ; $j < $note_avis; $j++) {
				echo '<span class="glyphicon glyphicon-star"></span>';
			}
		echo '</small>';
		echo '</h1>';
	
		// L'utilisateur est-il connecté ?
		if (userConnecte()) {
			echo '<h1 class="pull-right">
			<a href="' . RACINE_SITE . 'profil.php">
			<button type="submit" class="btn btn-lg">Réserver</button></h1></a>';

			// Réservation du produit table produit	
			$etat = 'reservation';
			req("REPLACE INTO produit (id_produit, id_salle, date_arrivee, date_depart, prix, etat) 
			VALUES ('$produit', '$salle', '$ligne[date_arrivee]', '$ligne[date_depart]', '$ligne[prix]', '$etat')");
			
			// Réservation du produit table commande
			$membre = htmlspecialchars($_SESSION['membre']['id_membre']);
			$date_enreg = date("Y-m-d H:i:s");
			
			req("REPLACE INTO commande (id_membre, id_produit, date_enregistrement) 
			VALUES ($membre, $produit, '$date_enreg')");
		
			}
		else 
			{
			echo '<h1 class="pull-right"><button type="submit" class="btn btn-lg">';
			echo '<a href="#connexion" rel="modal:open"';
			echo'" href="' . RACINE_SITE . 'connexion.php" >Connexion</a></button></h1>';
			}

		//					echo '<h4 class="pull-left"><a href="#connexion" rel="modal:open"';
		//				echo'" href="' . RACINE_SITE . 'connexion.php" >Connexion</a></h4>';

		echo '</div></div>';

		//L'image de la salle
		echo '<div class="row"><div class="col-md-8">';
		echo '<img class="img-responsive" src="photo/' . $ligne['photo'] . '" alt="">';
		echo '</div>';
	
		//Description entière
		echo '<div class="col-md-4">';
		echo '<h4>Description</h4>';
		echo '<p>' . $ligne['description'] . '</p>';

		// Localisation
		echo '<h4>Localisation</h4>';

        require('GoogleMapAPIv3.class.php');
				
        $gmap = new GoogleMapAPI(); 
        $gmap->setCenter('Nantes France');
        $gmap->setEnableWindowZoom(false);
		$gmap->setEnableAutomaticCenterZoom(true);
        $gmap->setDisplayDirectionFields(false);
        $gmap->setSize(100,60);
        //$gmap->setSize(1000,600);
        $gmap->setZoom(4);
        // $gmap->setLang('en');
        $gmap->setDefaultHideMarker(false);
		// Ajout d'un marqueur manuellement
		// $gmap->addMarkerByCoords(41.3,19.8,'Tirana');
         
		// Ajout d'un marqueur à partir d'une adresse
		$adresse = $ligne['adresse'] . ', ' . $ligne['cp'] . ' ' . $ligne['ville']; // "4 avenue de la Soeur Rosalie, 75013 Paris";
		//echo $adresse;
				
		//$gmap->addMarkerByAddress($adresse);
				
		$coordonnees = $gmap->geocoding($adresse);
		$latitude = $coordonnees[2];
		$longitude = $coordonnees[3];
				
		//echo 'Latitude = ' . $latitude;
		//echo ' Longitude = ' . $longitude;
				
		// Il faudrait stocker en base les informations $latitude et $longitude pour ne pas dépasser le quota d'utilisation gratuite de GoogleMaps
		$gmap->addMarkerByCoords($latitude,$longitude,'Mon bureau');
		//print_r($coordonnees);
				
				
		// Génération et affichage de la carte
		$gmap->generate();
        echo $gmap->getGoogleMap();
	
        ?>

        </div>

        </div>

		<!-- Informations complémentaires -->
        <div class="row">

            <div class="col-lg-12">
                <h4 class="page-header">Informations complémentaires</h4>
			</div>
			
			<div class="col-lg-4">
				<?php echo '<p><span class="glyphicon glyphicon-calendar"></span><b> Arrivée : </b>' . $date_arrivee_reformat . '</p>';
				echo '<p><span class="glyphicon glyphicon-calendar"></span><b> Départ : </b>' . $date_depart_reformat . '</p>'; ?>
			</div>

			<div class="col-lg-4">
				<?php echo '<p><span class="glyphicon glyphicon-user"></span><b> Capacité : </b>' . $ligne['capacite'] . '</p>';
				echo '<p><span class="glyphicon glyphicon-inbox"></span><b> Catégorie : </b>' . $ligne['categorie'] . '</p>'; ?>
			</div>

			<div class="col-lg-4">
				<?php echo '<p><span class="glyphicon glyphicon-map-marker"></span><b> Adresse : </b>' . $ligne['adresse'] . ' ' . $ligne['cp'] . ' ' . $ligne['ville'] .'</p>';
				echo '<p><span class="glyphicon glyphicon-euro"></span><b> Tarif : </b>' . $ligne['prix'] . ' €' . '</p>'; ?>
			</div>
			
		</div>
		
        <!-- Autres Produits -->
        <div class="row">

            <div class="col-lg-12">
                <h3 class="page-header">Autres produits</h3>
            </div>

			<?php 
			$resultat = req("SELECT s.photo, p.id_salle, p.id_produit 
						FROM salle s, produit p
						WHERE s.id_salle = p.id_salle
						AND p.id_produit != " . $produit . "
						AND s.ville = (select ville from salle where id_salle= ". $salle . ")
						ORDER by p.date_arrivee 
						LIMIT 0,4");
						
			while ($ligne3 = $resultat -> fetch_assoc()) {

				//Récupération de la note
				$note_avis = calcul_note($ligne3['id_salle']);

				echo '<div class="col-sm-3 col-xs-6">';
				echo '<a href="fiche_produit.php?salle=' . $ligne3['id_salle'] . '&produit=' . $ligne3['id_produit'] . '"><img class="img-responsive" src="photo/' . $ligne3['photo'] . '" alt=""></a>';
				echo '</div>';
			}

			?>


        </div>
        <!-- /.row -->
		
<?php } }  // fin du $_GET
echo$msg;

 ?>
		

        <div class="row">

            <div class="col-lg-12">
				<?php 

				// L'utilisateur est-il connecté ?
				if (userConnecte()) {
					if (empty($erreur)) // ne pas proposer de noter une salle ou un produit qui n'existe pas
					{
						echo '<h4 class="pull-left"><a href="#depot_commentaire" rel="modal:open"';
						echo'" href="' . RACINE_SITE . 'depot_commentaire.php?salle="' . $salle .'">Déposer un commentaire et une note</a></h4>';
					}
	
				}
				else 
				{
					echo '<h4 class="pull-left"><a href="#connexion" rel="modal:open"';
					echo'" href="' . RACINE_SITE . 'connexion.php" >Connexion</a></h4>';
					
				}
				?>
				<h4 class="pull-right"><a href="accueil.php">Retour vers le catalogue</a></h4>
			</div>
        </div>
        <!-- /.row -->
			

<?php 
require_once("formulaires.php");
require_once("inc/bas.front.inc.php");
?>
