<?php
require_once("inc/init.inc.php");

//debug($_SESSION['membre']);

// Si non connecté : redirection vers connexion
if(!userConnecte()){
	header('location: connexion.php');	
}

$title = 'Profil'; 
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg; 

// HTML de ma page
echo '<div id="page-wrapper">';

echo '<div class="container-fluid">';

echo '<h1>Mon Profil</h1>';
//----------------
echo '<ul>';
echo '	<li>Pseudo : <b>' . htmlspecialchars($_SESSION['membre']['pseudo']) . '</b></li>';
echo '	<li>Nom : <b>' . htmlspecialchars($_SESSION['membre']['nom']) . '</b></li>';
echo '	<li>Prénom : <b>' . htmlspecialchars($_SESSION['membre']['prenom']) . '</b></li>';
echo '	<li>Adresse email : <b>' . htmlspecialchars($_SESSION['membre']['email']) . '</b></li>';

$date_reformate = date("d/m/Y H:i:s", strtotime($_SESSION['membre']['date_enregistrement'])); 

echo '	<li>Date d\'enregistrement : <b>' . $date_reformate . '</b></li>';
echo '</ul>';
echo '</div></div>';
//----------------

echo '<h1>Mes Commandes</h1>';

$resultat = req("SELECT s.titre as Salle, s.ville, s.photo, p.date_arrivee, p.date_depart, c.date_enregistrement 
				FROM commande c, salle s, produit p 
				WHERE c.id_produit = p.id_produit
				AND p.id_salle = s.id_salle
				AND id_membre = " . $_SESSION['membre']['id_membre'] );

// teste le nombre de résultats
if ($resultat -> num_rows) {
	

				?>

    <div class="container-fluid">

		<div class="col-lg-12">
			<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
			<thead>
				<tr>
				<?php while($colonne = $resultat -> fetch_field()){
						echo '<th>' . $colonne -> name . '</th>';	
				} ?>
				</tr>
			</thead>
		
			<?php
			while($ligne = $resultat -> fetch_assoc()){
			echo '<tr>';
			foreach($ligne as $indice => $valeur){
				switch ($indice) {
					
					case 'photo':
						echo '<td><img height="80px" src="' . RACINE_SITE . 'photo/' . $valeur . '"/></td>';
						break;

					case 'date_arrivee':
					case 'date_depart':
					case 'date_enregistrement':
						$date_reformate = date("d/m/Y H:i:s", strtotime($valeur)); 
						echo '<td>' . $date_reformate . '</td>';
						break;
						
					default:
						echo '<td>' . $valeur . '</td>';
						break;
				}
			}

			
			echo '</tr>';
			}
			echo "</table>";
}
 else {
	
		$msg .= '<p style="color: white; background-color: green; padding: 10px;">Aucune commande à votre actif';	
		echo $msg;
		
}
	
			?>

<?php
require_once("formulaires.php");
require_once("inc/bas.front.inc.php");
?>