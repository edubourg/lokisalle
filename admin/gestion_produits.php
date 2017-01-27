<?php
require_once("../inc/init.inc.php");

if(!userConnecteAdmin()) {  
        header("location:../connexion.php");
    }

//-------------- SUPPRESSION produit EN BDD

if(isset($_GET['action']) && $_GET['action'] == "suppression" ){
	
	req("DELETE FROM produit WHERE id_produit='$_GET[id_produit]'");
	$msg .= '<p style="color: white; background-color: green; padding: 10px;">La produit id:' . $_GET['id_produit'] .  ' a été supprimé avec succès !</div>';
	$_GET['action'] = 'affichage';  
}

//-------------- AJOUT ET MODIFICATION produit EN BDD
if($_POST){
	//debug($_POST);

	// Pour parer aux failles XSS
	foreach ($_POST as $indice => $valeur) { 
		$_POST[$indice] = htmlspecialchars($valeur);
	}
	
	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}

	if (strlen($_POST['prix']) == 0 ) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Le prix du produit est manquant ! </p>';
		}

	// traitement pour récupérer id_salle $_POST['salle']
	$id_salle = intval(substr($_POST['salle'], 0, 2));

	// Transforme date
	$date_en_arrivee = convertDate($_POST['date_arrivee']);
	$date_en_depart = convertDate($_POST['date_depart']);

	// Contrôle des dates
	$date_actuelle = Date("Y-m-d"); // Obtention de la date actuelle
	$ts_date_actuelle = strtotime($date_actuelle);
	$ts_date_en_arrivee = strtotime($date_en_arrivee);
	$ts_date_en_depart = strtotime($date_en_depart);

	if (($ts_date_en_arrivee < $ts_date_actuelle) || ($ts_date_en_depart < $ts_date_actuelle)) {
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Attention ! Création d\'un produit sur des dates antérieures !</p>'; 
		
	}
	
	if ($ts_date_en_arrivee > $ts_date_en_depart ) {
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Attention ! La date d\'arrivée est après la date de départ !</p>'; 
		
	}

	if(empty($msg))
		{
	
			// La requête suivante donne t'elle des résultats ?
			$resultat = req("SELECT COUNT(id_produit) 
				FROM produit 
				WHERE (date_arrivee >= '" . $date_en_arrivee . "' AND date_depart <= '" . $date_en_depart . "') OR 
				(date_depart >= '" . $date_en_arrivee . "' AND date_depart <= '" . $date_en_depart . "') OR 
				(date_arrivee >= '" . $date_en_arrivee . "' AND date_arrivee <= '" . $date_en_depart . "') OR 
				(date_arrivee >= '" . $date_en_arrivee . "' AND date_depart >= '" . $date_en_depart . "')");

			if (!empty($resultat->num_rows))
			{
				// affectation etat
				$etat = $_POST['etat'];

				// Et si la salle est supprimée ? ne pas permettre la modification de prix
				if (!empty($_POST['id_salle']))
				{
					req("REPLACE INTO produit (id_produit, id_salle, date_arrivee, date_depart, prix, etat) VALUES ('$_POST[id_produit]', '$id_salle', '$date_en_arrivee', '$date_en_depart', '$_POST[prix]', '$etat')");
					$msg .= '<p style="color: white; background-color: green; padding: 10px;">Le nouveau produit a été enregistré ou modifié !</p>';
				}
				else // affichage message d'erreur
				{
					$msg .= '<p style="color: white; background-color: red; padding: 10px;">Ce produit ne peut plus être modifié car la salle a été supprimée !</p>';
				}
			}
			else
				{
				$msg .= '<p style="color: white; background-color: red; padding: 10px;">Chevauchement de dates dans la réservation, entre ' . 
				$date_en_arrivee . ' et ' . $date_en_depart . ' !</p>';
				}
				
			//header("location:gestion_produits.php?action=affichage");
		}
}

$title="Gestion Produits";
require("../inc/haut.back.inc.php");
echo $msg;

?>

<!-- HTML -->
<div id="page-wrapper">

<p><a href="?action=ajout">Ajouter un produit</a></p>
<hr/>

<?php

// Affichage de la table produit

if(isset($_GET['action']) && $_GET['action'] == "affichage" ){

$resultat = req("SELECT * FROM produit");

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

			switch($indice) {
				
				case 'id_produit':
					$id_produit = $valeur;
					echo '<td>' . $valeur . '</td>';
					break;
					
				case 'id_salle':
					$requete2 = "SELECT s.titre FROM salle s, produit p WHERE p.id_salle = s.id_salle and p.id_produit = " . $id_produit;
					$resultat2 =  $mysqli->query($requete2);

					if ($resultat2 -> num_rows != 0) {
						while($ligne2 = $resultat2->fetch_assoc()){
							if ($valeur <= 9) 
								{	echo '<td>0' . $valeur . ' - ' . $ligne2['titre'] . '</td>'; }
							else
								{	echo '<td>' . $valeur . ' - ' . $ligne2['titre'] . '</td>'; }
							}
					}
					else
						{
							echo '<td> Salle supprimée </td>';
							}
					
					break;
					
				case 'date_arrivee':
				case 'date_depart':
					// Traitement des dates pour transformer au format DD/MM/YYYY HH:MM
					$date_reformate = date("d/m/Y H:i:s", strtotime($valeur)); 
					echo '<td>' . $date_reformate . '</td>';
					break;

				default:
					echo '<td>' . $valeur . '</td>';
					break;
					
				}			
			}

			
			// actions : affichage, modification, suppression	
			echo '<td><a href="?action=affichage&id_produit=' . $ligne['id_produit'] . '">';
			echo '<span class="glyphicon glyphicon-zoom-out"></span>&nbsp;</a>';
			echo '<a href="?action=modification&id_produit=' . $ligne['id_produit'] . '">';
			echo '<span class="glyphicon glyphicon-edit"></span>&nbsp;</a>';
			echo '<a href="?action=suppression&id_produit=' . $ligne['id_produit'] . '">';
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
if(isset($_GET['action']) && ($_GET['action'] == "ajout" || $_GET['action'] == "modification")){
	
	if(isset($_GET['id_produit'])){
		$resultat = req("SELECT * FROM produit WHERE id_produit='$_GET[id_produit]'");
		$produit_actuel = $resultat -> fetch_assoc();
		//debug($produit_actuel);
		echo '<h1>Correction d\'un produit</h1>';
	}
	else{
		echo '<h1>Ajout d\'un produit</h1>';
	}

?>

	<!---------------- FORMULAIRE HTML ---------------------->
            <div class="container-fluid">

                <div class="row">
                    <form role="form" action="" method="post">
						<div class="col-lg-6">

							<input  type="hidden" name="id_produit" value="<?php if(isset($produit_actuel)){echo $produit_actuel['id_produit'];} else { echo '0'; }?>" />
							<input  type="hidden" name="id_salle" value="<?php if(isset($produit_actuel)){echo $produit_actuel['id_salle'];}?>" />

							<!-- formulaire de date --> 
							<div class="form-group">
								<label>Date d'arrivée</label>
								<div class="input-group" id="date_arrivee">
									<div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
									<input type='text' class="form-control" name="date_arrivee" 
									value="<?php if(isset($produit_actuel)){
										$date_reformate = date("d/m/Y H:i:s", strtotime($produit_actuel['date_arrivee']));
										echo $date_reformate;}?>" />
								</div>
							</div>
							
							<div class="form-group">
								<label>Date de départ</label>
								<div class="input-group" id="date_depart">
									<div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
									<input type='text' class="form-control" name="date_depart" 
									value="<?php if(isset($produit_actuel)){
										$date_reformate = date("d/m/Y H:i:s", strtotime($produit_actuel['date_depart']));
										echo $date_reformate;}?>" />
								</div>
							</div>


						</div>		

						<div class="col-lg-6">

							<div class="form-group">
								<label>Salle</label>
								<select class="form-control" name="salle">
								<?php 
								$requete = "SELECT id_salle, titre, adresse, cp, ville, capacite FROM salle ORDER BY 1";
								$resultat = $mysqli->query($requete);
								while($ligne = $resultat->fetch_assoc()){

									if ($produit_actuel['id_salle'] == $ligne['id_salle']) {
										echo '<option selected>'.$ligne['id_salle'].' - '.$ligne['titre'].' - '.$ligne['adresse'].' - '.$ligne['cp'].' - '.$ligne['ville'].' - '.$ligne['capacite'].' pers</option>'; 
										}
									 else {
										 echo '<option>'.$ligne['id_salle'].' - '.$ligne['titre'].' - '.$ligne['adresse'].' - '.$ligne['cp'].' - '.$ligne['ville'].' - '.$ligne['capacite'].' pers</option>';
									 }
								}
								?>
								</select>
							</div>

                            <div class="form-group">
								<label>Tarif</label>
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></div>
									<input type="text" class="form-control" name="prix" placeholder="prix en euros"
									value="<?php if(isset($produit_actuel)){echo $produit_actuel['prix'];}?>">
								</div>
							</div>

							<div class="form-group">
								<label>Etat</label>
								<select class="form-control" name="etat">
										<option value="libre" <?php if(isset($produit_actuel) && $produit_actuel['etat'] == 'reunion'){echo 'selected';}?>  >libre</option>
										<option value="reservation" <?php if(isset($produit_actuel) && $produit_actuel['etat'] == 'bureau'){echo 'selected';}?>  >reservation</option>
								</select>
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
require_once("../inc/datescriptjs.inc.php");
require_once("../inc/bas.back.inc.php");
?>

