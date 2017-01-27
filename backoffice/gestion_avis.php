<?php
require_once("../inc/init.inc.php");

if(!userConnecteAdmin()) {  
        header("location:../connexion.php");
    }

//-------------- SUPPRESSION avis EN BDD

if(isset($_GET['action']) && $_GET['action'] == "suppression" ){
	
	req("DELETE FROM avis WHERE id_avis='$_GET[id_avis]'");
	$msg .= '<p style="color: white; background-color: green; padding: 10px;">Avis id:' . $_GET['id_avis'] .  ' a été supprimé avec succès !</p>';
	$_GET['action'] = 'affichage';  
}

//-------------- MODIFICATION avis EN BDD
if($_POST){
	debug($_POST);

	// Pour parer aux failles XSS
	foreach ($_POST as $indice => $valeur) { 
		$_POST[$indice] = htmlspecialchars($valeur);
	}
	
	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}
	
	// Conversion int pour note
	$note = intval(substr($_POST['note'], 0, 2));

	// Et si la salle est supprimée ?
	if (!empty($_POST['id_salle']))
	{
		req("REPLACE INTO avis (id_avis, id_membre, id_salle, commentaire, note, date_enregistrement) VALUES ('$_POST[id_avis]', '$_POST[id_membre]', '$_POST[id_salle]', '$_POST[commentaire]', '$note', '$_POST[date_enregistrement]')");
	}
	else
	{
		req("REPLACE INTO avis (id_avis, id_membre, commentaire, note, date_enregistrement) VALUES ('$_POST[id_avis]', '$_POST[id_membre]', '$_POST[commentaire]', '$note', '$_POST[date_enregistrement]')");
	}	
		
	$msg .= '<p style="color: white; background-color: green; padding: 10px;">Le nouveau avis a été enregistré !</p>';
	$_GET['action'] = "affichage";
	//header("location:gestion_avis.php?action=affichage");
 
}

$title="Gestion avis";
require_once("../inc/haut.back.inc.php");
echo $msg;

?>

<!-- HTML -->
<div id="page-wrapper">

<br />

<?php

// Affichage de la table avis

if(isset($_GET['action']) && $_GET['action'] == "affichage" ){

$resultat = req("SELECT * FROM avis");
?>

    <div class="container-fluid">

		<div class="col-lg-12">
			<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped" 
			id="pagination" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
				<?php while($colonne = $resultat -> fetch_field()){
					if ($colonne->name == 'id_salle') {
						echo '<th> Identifiant - Nom Salle </th>';
					}
					else
					{
						echo '<th>' . $colonne -> name . '</th>';	
					} 
				} ?>
				<th>actions</th>
				</tr>
			</thead>
		
			<?php
			while($ligne = $resultat -> fetch_assoc()){
			echo '<tr>';
			foreach($ligne as $indice => $valeur){

				// récupération de l'id de l'avis pour les deux colonnes suivantes
				if ($indice == 'id_avis' ) {
					$id_avis = $valeur;
				}

				//récupération de l'email et concaténation avec id_membre
				switch ($indice) {
					
						
					case 'id_membre':
							$requete2 = "SELECT m.id_membre, m.email FROM avis a, membre m WHERE a.id_membre = m.id_membre and a.id_avis = " . $id_avis;
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
						
					case 'id_salle':
						$requete2 = "SELECT s.titre FROM salle s, avis p WHERE p.id_salle = s.id_salle and p.id_avis = " . $id_avis;
						$resultat2 =  $mysqli->query($requete2);

						if ($resultat2 -> num_rows != 0) {
							while($ligne2 = $resultat2->fetch_assoc()){
								echo '<td>' . $valeur . ' - ' . $ligne2['titre'] . '</td>';
							}
						}
						else
						 {
							echo '<td> Salle supprimée </td>';
						 }
						
						break;
					
					case 'date_enregistrement':
							// Traitement des dates pour transformer au format DD/MM/YYYY HH:MM
							$date_enregistrement = date("d/m/Y H:i:s", strtotime($valeur)); 
							echo '<td>' . $date_enregistrement . '</td>';
							break;

					case 'note':
						$note_avis = 5;
						if ($valeur < 16) { $note_avis--; }
						if ($valeur < 12) { $note_avis--; }
						if ($valeur < 8) { $note_avis--; }
						if ($valeur < 4) { $note_avis--; }

						echo '<td>';
						for ($j = 0 ; $j < $note_avis; $j++) {
							echo '<span class="glyphicon glyphicon-star"></span>';
						}
						echo '</td>';
						break;
						
					default:
						echo '<td>' . $valeur . '</td>';
						break;
				}
			}		
			
			// actions : affichage, modification, suppression	
			echo '<td><a href="?action=affichage&id_avis=' . $ligne['id_avis'] . '">';
			echo '<span class="glyphicon glyphicon-zoom-out"></span>&nbsp;</a>';
			echo '<a href="?action=modification&id_avis=' . $ligne['id_avis'] . '">';
			echo '<span class="glyphicon glyphicon-edit"></span>&nbsp;</a>';
			echo '<a href="?action=suppression&id_avis=' . $ligne['id_avis'] . '">';
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

// Affichage de la table 
if(isset($_GET['action']) && ($_GET['action'] == "modification")){
	
	if(isset($_GET['id_avis'])){
		$resultat = req("SELECT * FROM avis WHERE id_avis='$_GET[id_avis]'");
		$avis_actuel = $resultat -> fetch_assoc();
		
		// Récupération de l'email de la personne
		$requete2 = "SELECT m.pseudo, m.nom, m.prenom, m.email, m.date_enregistrement FROM avis a, membre m WHERE a.id_membre = m.id_membre and a.id_avis = " . $_GET['id_avis'];
		$resultat2 =  $mysqli->query($requete2);
		$ligne2 = $resultat2->fetch_assoc();

		// Traitement des dates pour transformer au format DD/MM/YYYY HH:MM
		$date_enregistrement = date("d/m/Y H:i:s", strtotime($ligne2['date_enregistrement'])); 

		//debug($ligne2);
		echo '<h1>Correction de l\'avis émis par : </h1>';
		echo '<ul>';
		echo '	<li>Pseudo : <b>' . $ligne2['pseudo'] . '</b></li>';
		echo '	<li>Nom : <b>' . $ligne2['nom'] . '</b></li>';
		echo '	<li>Prénom : <b>' . $ligne2['prenom'] . '</b></li>';
		echo '	<li>Adresse email : <b>' . $ligne2['email'] . '</b></li>';
		echo '	<li>Date d\'enregistrement : <b>' . $date_enregistrement . '</b></li>';
		echo '</ul>';
		
		
		
	}
?>

	<!---------------- FORMULAIRE HTML ---------------------->
  

            <div class="container-fluid">

                <div class="row">
                    <form role="form" action="" method="post">
						<div class="col-lg-12">

							<!-- Les champs non modifiables -->
							<input  type="hidden" name="id_avis" value="<?php if(isset($avis_actuel)){echo $avis_actuel['id_avis'];} else { echo '0'; }?>" />
							<input  type="hidden" name="id_membre" value="<?php if(isset($avis_actuel)){echo $avis_actuel['id_membre'];} else { echo '0'; }?>" />
							<input  type="hidden" name="id_salle" value="<?php if(isset($avis_actuel)){echo $avis_actuel['id_salle'];} else { echo '0'; }?>" />
                            <input type="hidden" name="note" value="<?php if(isset($avis_actuel)){echo $avis_actuel['note'];} else { echo '0'; }?>" />
                            <input type="hidden" name="date_enregistrement" value="<?php if(isset($avis_actuel)){echo $avis_actuel['date_enregistrement'];} else { echo '0'; }?>" />
							
                            <div class="form-group">
								<label>Commentaire</label>
                                <input type="text" class="form-control" name="commentaire" placeholder="mon commentaire" value="<?php if(isset($avis_actuel)){echo $avis_actuel['commentaire'];}?>">
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-default">Enregistrer</button>
								<button type="reset" class="btn btn-default">Remise à zéro</button>
                            </div>
							
						</div>
					</form>	

            </div>
            <!-- /.container-fluid -->

</div> <!-- /#page-wrapper -->


<?php
}
require_once("../inc/bas.back.inc.php");
?>

