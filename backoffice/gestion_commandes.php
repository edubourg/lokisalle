<?php
require_once("../inc/init.inc.php");

if(!userConnecteAdmin()) {  
        header("location:../connexion.php");
    }

//-------------- SUPPRESSION commande EN BDD

if(isset($_GET['action']) && $_GET['action'] == "suppression" ){
	
	req("DELETE FROM commande WHERE id_commande='$_GET[id_commande]'");
	$msg .= '<p style="color: white; background-color: green; padding: 10px;">La commande id:' . $_GET['id_commande'] .  ' a été supprimé avec succès !</p>';
	$_GET['action'] = 'affichage';  
}

//-------------- MODIFICATION commande EN BDD
if($_POST){
	//debug($_POST);

	// Pour parer aux failles XSS
	foreach ($_POST as $indice => $valeur) { 
		$_POST[$indice] = htmlspecialchars($valeur);
	}
	
	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}
	
	req("REPLACE INTO commande (id_commande, id_membre, id_produit, prix, date_enregistrement) VALUES ('$_POST[id_commande]', '$_POST[id_membre]', '$_POST[id_produit]', '$_POST[prix]', '$_POST[date_enregistrement]')");

	$msg .= "<div class='validation'>La nouvelle commande a été enregistrée !</div>";
	$_GET['action'] = "affichage";
	header("location:gestion_commande.php");
 
}

$title="Gestion commande";
require_once("../inc/haut.back.inc.php");
echo $msg;

?>

<!-- HTML -->
<div id="page-wrapper">

<br />

<?php

// Affichage de la table commande

if(isset($_GET['action']) && $_GET['action'] == "affichage" ){

$resultat = req("SELECT * FROM commande");

?>

    <div class="container-fluid">

		<div class="col-lg-12">
			<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped" 
			id="pagination" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
				<?php while($colonne = $resultat -> fetch_field()){
				/*	if ($colonne->name == 'id_salle') {
						echo '<th> Identifiant - Nom Salle </th>';
					}
					else
					{*/
						echo '<th>' . $colonne -> name . '</th>';	
					//} 
				} ?>
				<th>actions</th>
				</tr>
			</thead>
		
			<?php
			while($ligne = $resultat -> fetch_assoc()){
			echo '<tr>';
			foreach($ligne as $indice => $valeur){

				//récupération de l'email et concaténation avec id_membre
				switch ($indice) {
					
					case 'id_commande':
						$id_commande = $valeur;
						echo '<td>' . $valeur . '</td>';
						break;
					
					case 'id_membre':
							$requete2 = "SELECT m.id_membre, m.email FROM commande c, membre m WHERE c.id_membre = m.id_membre and c.id_commande = " . $id_commande;
							$resultat2 =  $mysqli->query($requete2);
							if ($resultat2 -> num_rows != 0) {
								while($ligne2 = $resultat2->fetch_assoc()){
									echo '<td>' . $ligne2['id_membre'] . ' - ' . $ligne2['email'] . '</td>';
								};
							}
							else 
							{
								echo '<td> Membre supprimé </td>';
							}

						break;
						
					case 'id_produit': 
						$requete2 = "SELECT p.id_produit, s.titre, p.date_arrivee, p.date_depart FROM produit p, salle s, commande c 
									WHERE c.id_produit = p.id_produit AND p.id_salle = s.id_salle AND c.id_commande = " . $id_commande;
						$resultat2 =  $mysqli->query($requete2);

						if ($resultat2 -> num_rows != 0) {
						
							while($ligne2 = $resultat2->fetch_assoc()){

								// Traitement des dates pour transformer au format DD/MM/YYYY HH:MM
								$date_arrivee = date("d/m/Y H:i", strtotime($ligne2['date_arrivee'])); 
								$date_depart = date("d/m/Y H:i", strtotime($ligne2['date_depart'])); 
							
								echo '<td>' . $valeur . ' - ' . $ligne2['titre'] . ' (' . $date_arrivee . ' au ' . $date_depart . ')</td>';
								}
							}
							else 
							{
								echo '<td> Produit supprimé </td>';
							}
							
						break;

					case 'date_enregistrement':
							// Traitement des dates pour transformer au format DD/MM/YYYY HH:MM
							$date_enregistrement = date("d/m/Y H:i:s", strtotime($valeur)); 
							echo '<td>' . $date_enregistrement . '</td>';
							break;
						
					default:
						echo '<td>' . $valeur . '</td>';
						break;
				}
			}		
			
			// actions : affichage, modification, suppression	
			echo '<td><a href="?action=affichage&id_commande=' . $ligne['id_commande'] . '">';
			echo '<span class="glyphicon glyphicon-zoom-out"></span>&nbsp;</a>';
			//echo '<a href="?action=modification&id_commande=' . $ligne['id_commande'] . '">';
			//	echo '<span class="glyphicon glyphicon-edit"></span>&nbsp;</a>';
			echo '<a href="?action=suppression&id_commande=' . $ligne['id_commande'] . '">';
			echo '<span class="glyphicon glyphicon-trash"></span></div></a></td>';
		
			echo '</tr>';
			}
			echo "</table>";
			?>	
			</div>
		</div>
	</div>

<?php								
}

?>

</div> <!-- /#page-wrapper -->


<?php
require_once("../inc/bas.back.inc.php");
?>

