<?php
require_once("../inc/init.inc.php");

if(!userConnecteAdmin()) {  
        header("location:../connexion.php");
    }

//-------------- SUPPRESSION SALLE EN BDD

if(isset($_GET['action']) && $_GET['action'] == "suppression" ){
	$salle = req("SELECT * FROM salle WHERE id_salle='$_GET[id_salle]'");
	$salle_a_sup = $salle -> fetch_assoc();
	$chemin_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE .  '/photo/' .$salle_a_sup['photo'];

	if(!empty($salle_a_sup['photo']) && file_exists($chemin_photo_a_supprimer )){
		unlink($chemin_photo_a_supprimer);
	}
	
	req("DELETE FROM salle WHERE id_salle='$_GET[id_salle]'");
	$msg .= '<p style="color: white; background-color: green; padding: 10px;">La salle id:' . $_GET['id_salle'] .  ' a été supprimée avec succès !</div>';
	$_GET['action'] = 'affichage';  
}

//-------------- AJOUT ET MODIFICATION SALLE EN BDD
if($_POST){
	//debug($_POST);
	$photo_bdd = ''; 

	// Pour parer aux failles XSS
	foreach ($_POST as $indice => $valeur) { 
		$_POST[$indice] = htmlspecialchars($valeur);
	}
	
	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}

	if (strlen($_POST['titre']) == 0 ) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir le titre de la salle</p>';
		}

	if (strlen($_POST['description']) == 0 ) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir la description de la salle</p>';
		}

	if (strlen($_POST['adresse']) == 0 ) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir l\'adresse de la salle</p>';
		}

	if (strlen($_POST['cp']) == 0 ) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir le code postal de la salle</p>';
		}

	// récupération de la photo actuelle
	if(isset($_GET['action']) && $_GET['action'] == 'modification' ){ 
		$photo_bdd = $_POST['photo_actuelle']; // Cf remplissage formulaire
	}
	
	// Gestion de l'ajout de photo
	if(!empty($_FILES['photo']['name'])){
		//debug($_FILES);
		$nom_photo = $_POST['titre'] . '_' . $_FILES['photo']['name'];
		$photo_bdd = $nom_photo; 
		$photo_dossier = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $nom_photo;

		// Contrôle du type de fichier
		$type_fichier = $_FILES['photo']['type'];
		switch ($type_fichier)
			{
				case 'image/png':
				case 'image/jpg':
				case 'image/jpeg': 
				case 'image/gif':
					copy($_FILES['photo']['tmp_name'], $photo_dossier);
					break;
					
				default:
					$msg .= '<p style="color: white; background-color: red; padding: 10px;">Tentative d\'upload d\'un fichier interdit : gif, png, jpeg, jpg autorisé !</p>'; 
					break;
			}
	}


	if(empty($msg))
		{
	
			req("REPLACE INTO salle (id_salle, titre, description, photo, pays, ville, adresse, cp, capacite, categorie) VALUES ('$_POST[id_salle]', '$_POST[titre]', '$_POST[description]', '$photo_bdd', '$_POST[pays]', '$_POST[ville]', '$_POST[adresse]', '$_POST[cp]', '$_POST[capacite]', '$_POST[categorie]')");
		
			echo $msg;
			//$_GET['action'] = "affichage";
			//header("location:gestion_salles.php?action=affichage");
		}
}

$title="Gestion Salles";
require_once("../inc/haut.back.inc.php");
echo $msg;

?>

<!-- HTML -->
<div id="page-wrapper">

<p><a href="?action=ajout">Ajouter une salle</a></p>
<hr/>

<?php

// Affichage de la table salle

if(isset($_GET['action']) && $_GET['action'] == "affichage" ){

$resultat = req("SELECT * FROM salle");

?>

    <div class="container-fluid">

		<div class="col-lg-12 col-xs-12 col-sm-12">
			<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped" 
			id="pagination" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
				<?php while($colonne = $resultat -> fetch_field()){
					echo '<th>' . $colonne -> name . '</th>';	} ?>
				<th>actions</th>
				</tr>
			</thead>
		
			<?php
			while($ligne = $resultat -> fetch_assoc()){
			echo '<tr>';
			foreach($ligne as $indice => $valeur){
				if($indice == 'photo'){
					echo '<td>';
				    echo '<div class="col-lg-12 col-xs-12 col-sm-12">';
					echo '<a href="#" class="thumbnail" data-toggle="modal" data-target="#lightbox">'; 
					echo '<img height="80px" src="' . RACINE_SITE . 'photo/' . $valeur . '" alt="..." />';
					echo '</a></div></td>';
					}

				else{
					echo '<td>' . $valeur . '</td>';
				}
			}

			// actions : affichage, modification, suppression	
			echo '<td><a href="?action=affichage&id_salle=' . $ligne['id_salle'] . '">';
			echo '<span class="glyphicon glyphicon-zoom-out"></span>&nbsp;</a>';
			echo '<a href="?action=modification&id_salle=' . $ligne['id_salle'] . '">';
			echo '<span class="glyphicon glyphicon-edit"></span>&nbsp;</a>';
			echo '<a href="?action=suppression&id_salle=' . $ligne['id_salle'] . '">';
			echo '<span class="glyphicon glyphicon-trash"></span></div></a></td>';
			
			echo '</tr>';
			}
			echo "</table>";
			?>	
			</div>
		</div>
	</div>

	<!-- Fermeture LightBox -->
	<div id="lightbox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<button type="button" class="close hidden" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-content">
				<div class="modal-body">
					<img src="" alt="" />
				</div>
			</div>
		</div>
	</div>
	
<?php								
}

// Affichage de la table 
if(isset($_GET['action']) && ($_GET['action'] == "ajout" || $_GET['action'] == "modification")){
	
	if(isset($_GET['id_salle'])){
		$resultat = req("SELECT * FROM salle WHERE id_salle='$_GET[id_salle]'");
		$salle_actuelle = $resultat -> fetch_assoc();
		//debug($salle_actuelle);
		echo '<h1>Correction d\'une salle</h1>';
	}
	else{
		echo '<h1>Ajout d\'une salle</h1>';
	}

?>

	<!---------------- FORMULAIRE HTML ---------------------->
 

            <div class="container-fluid">

                <div class="row">
                    <form role="form" action="" method="post" enctype="multipart/form-data">
						<div class="col-lg-6">

							<input  type="hidden" name="id_salle" class="form-control" value="<?php if(isset($salle_actuelle)){echo $salle_actuelle['id_salle'];} else { echo '0'; }?>" />
								
							<div class="form-group">
								<label for="titre">Titre</label>
								<input type="text" class="form-control" name="titre" placeholder="Titre de la salle" value="<?php if(isset($salle_actuelle)){echo $salle_actuelle['titre'];}?>">
							</div>
								
 							<div class="form-group">
								<label>Description</label>
								<textarea name="description" class="form-control" rows="3" placeholder="Description de la salle"><?php if(isset($salle_actuelle)){echo $salle_actuelle['description'];}?></textarea>
							</div>
								
							<div class="form-group">
								<label>Photo</label>
								<?php if(isset($salle_actuelle) && !empty($salle_actuelle['photo'])){
								echo '<img src="' . RACINE_SITE . 'photo/' . $salle_actuelle['photo'] . '" width="100px"/>';
								echo '<input type="hidden" name="photo_actuelle" value="' . $salle_actuelle['photo'] . '"/>';
								}
								?>
								<input type="file" class="form-control" name="photo">
							</div>
								
							<div class="form-group">
								<label>Capacité<span class="mandatory">*</span></label></label>
								<select class="form-control" name="capacite">
									<?php
										$i= 1;
										while ($i <= 100) {
											if(isset($salle_actuelle) &&  $salle_actuelle['capacite'] == $i) {
													echo '<option value="' . $i . '" selected>' . $i . '</option>';
												}
											else
												{
													echo '<option value="' . $i . '">' . $i . '</option>';
												}
											$i++;
									} ?>
								</select>
								
							</div>
	
							<div class="form-group">
								<label>Catégorie</label>
								<select class="form-control" name="categorie">
										<option value="reunion" <?php if(isset($salle_actuelle) && $salle_actuelle['categorie'] == 'reunion'){echo 'selected';}?>  >reunion</option>
										<option value="bureau" <?php if(isset($salle_actuelle) && $salle_actuelle['categorie'] == 'bureau'){echo 'selected';}?>  >bureau</option>
										<option value="formation" <?php if(isset($salle_actuelle) && $salle_actuelle['categorie'] == 'formation'){echo 'selected';}?>>formation</option>
								</select>
							</div>
							
						</div>
						
						<div class="col-lg-6">

							<div class="form-group">
                                <label>Pays</label>
								<select class="form-control" name="pays">
									<option value="France" <?php if(isset($salle_actuelle) && $salle_actuelle['pays'] == 'France'){echo 'selected';}?>  >France</option>
									<option value="Italie" <?php if(isset($salle_actuelle) && $salle_actuelle['pays'] == 'Italie'){echo 'selected';}?>  >Italie</option>
									<option value="Espagne" <?php if(isset($salle_actuelle) && $salle_actuelle['pays'] == 'Espagne'){echo 'selected';}?>  >Espagne</option>
								</select>                     
							</div>
							
							<div class="form-group">
                                <label>Ville</label>
								<select class="form-control" name="ville">
									<option value="Paris" <?php if(isset($salle_actuelle) && $salle_actuelle['ville'] == 'ville'){echo 'selected';}?>  >Paris</option>
									<option value="Lyon" <?php if(isset($salle_actuelle) && $salle_actuelle['ville'] == 'ville'){echo 'selected';}?>  >Lyon</option>
									<option value="Marseille" <?php if(isset($salle_actuelle) && $salle_actuelle['ville'] == 'ville'){echo 'selected';}?>  >Marseille</option>
                                </select>
							</div>
								
							<div class="form-group">
                                <label>Adresse</label>
                                <textarea name="adresse" class="form-control" rows="3" placeholder="Adresse de la salle"><?php if(isset($salle_actuelle)){echo $salle_actuelle['adresse'];}?></textarea>
							</div>
								
							<div class="form-group">
                                <label>Code Postal</label>
                                <input type="text" class="form-control" name="cp" placeholder="Code postal de la salle" value="<?php if(isset($salle_actuelle)){echo $salle_actuelle['cp'];}?>">
								<br />
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

