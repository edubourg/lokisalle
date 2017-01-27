<?php
require_once("../inc/init.inc.php");

if(!userConnecteAdmin()) {  
        header("location:../connexion.php");
    }

//-------------- SUPPRESSION membre EN BDD

if(isset($_GET['action']) && $_GET['action'] == "suppression" ){
	$membre = req("SELECT * FROM membre WHERE id_membre='$_GET[id_membre]'");
	$membre_a_sup = $membre -> fetch_assoc();

	req("DELETE FROM membre WHERE id_membre='$_GET[id_membre]'");
	$msg .= '<p style="color: white; background-color: green; padding: 10px;">Le membre id:' . $_GET['id_membre'] .  ' a été supprimé avec succès !</p>';
	echo $msg;
	$_GET['action'] = 'affichage';  
}

//-------------- AJOUT ET MODIFICATION membre EN BDD
if($_POST){
	//debug($_POST);
	
	// Pour parer aux failles XSS
	foreach ($_POST as $indice => $valeur) { 
		$_POST[$indice] = htmlspecialchars($valeur);
	}
	
	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}

	if (strlen($_POST['pseudo']) == 0 ) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir un pseudo</p>';
		}

	if (strlen($_POST['mdp']) == 0) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir un mot de passe</p>';
	}

	if (strlen($_POST['nom']) == 0 ) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir un nom</p>';
		}
		
	if (strlen($_POST['prenom']) == 0) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir un prénom</p>';
	}

	if (strlen($_POST['email']) == 0) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir un email</p>';
	}

	// test adresse mail valide
	$email = $_POST['email'];
	$_POST['email'] = str_replace(array("\n","\r",PHP_EOL),'',$email); // suppression du retour chariot adresse mail

	if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
			$msg .=  '<p style="color: white; background-color: red; padding: 10px;">' . $_POST['email'] . 'n\'est pas une adresse email valide.</p>';
	}	

	// validation pseudo
	$verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $_POST['pseudo']);  
	
	if(!$verif_caractere || strlen($_POST['pseudo']) < 6 || strlen($_POST['pseudo']) > 20 ){
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Le pseudo doit contenir entre 5 et 20 caractères.</p>';
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Caractères acceptés : Lettres de A à Z et chiffres de 0 à 9.</p>';
	}
	
	// validation mdp
	$verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $_POST['mdp']);  
	
	if(!$verif_caractere || strlen($_POST['mdp']) < 5  ){
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Le mpt de passe doit avoir plus de cinq caractères.</p>';
		$msg .= '<p style="color: white; background-color: red; padding: 10px;">Caractères acceptés : Lettres de A à Z et chiffres de 0 à 9.</p>';
	}

	if (strlen($_POST['date_enregistrement']) == 0) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir une date d\'enregistrement</p>';
	}

	if(empty($msg))
		{
			// Transforme date
			$date_en_enreg = convertDate($_POST['date_enregistrement']);
	
			req("REPLACE INTO membre (id_membre, pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) 
			VALUES ('$_POST[id_membre]', '$_POST[pseudo]', '$_POST[mdp]', '$_POST[nom]', '$_POST[prenom]', '$_POST[email]', '$_POST[civilite]', '$_POST[statut]', '$date_en_enreg')");

			$msg .= '<p style="color: white; background-color: green; padding: 10px;">Le nouveau membre a été enregistré !</p>';
			//header("location:gestion_salles.php?action=affichage");
		}	

}

$title="Gestion membres";
require_once("../inc/haut.back.inc.php");
echo $msg;

?>

<!-- HTML -->
<div id="page-wrapper">

<p><a href="?action=ajout">Ajouter un membre</a></p>
<hr/>

<?php

// Affichage de la table membre

if(isset($_GET['action']) && $_GET['action'] == "affichage" ){

$resultat = req("SELECT * FROM membre");

?>

    <div class="container-fluid">

		<div class="col-lg-12">
			<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped" 
			id="pagination" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
				<?php while($colonne = $resultat -> fetch_field()){
					if ($colonne-> name != 'mdp') { // non affichage du mot de passe
						echo '<th>' . $colonne -> name . '</th>';	} 
					} ?>
				<th>actions</th>
				</tr>
			</thead>
		
			<?php
			while($ligne = $resultat -> fetch_assoc()){
			echo '<tr>';
			foreach($ligne as $indice => $valeur){

				switch($indice) {
				
					case 'civilite':
						if ($valeur == 'm')
							{ echo '<td> Homme </td>'; }
						else
							{ echo '<td> Femme </td>'; }
						break;
						
					case 'date_enregistrement':
					// Traitement des dates pour transformer au format DD/MM/YYYY HH:MM
						$date_enregistrement = date("d/m/Y H:i:s", strtotime($valeur)); 
						echo '<td>' . $date_enregistrement . '</td>';
						break;
						
					case 'mdp': // ne rien afficher
						break;
						
					default:
						echo '<td>' . $valeur . '</td>';
						break;
				}
			}
		
			// actions : affichage, modification, suppression	
			echo '<td><a href="?action=affichage&id_membre=' . $ligne['id_membre'] . '">';
			echo '<span class="glyphicon glyphicon-zoom-out"></span>&nbsp;</a>';
			echo '<a href="?action=modification&id_membre=' . $ligne['id_membre'] . '">';
			echo '<span class="glyphicon glyphicon-edit"></span>&nbsp;</a>';
			echo '<a href="?action=suppression&id_membre=' . $ligne['id_membre'] . '">';
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
	
	if(isset($_GET['id_membre'])){
		$resultat = req("SELECT * FROM membre WHERE id_membre='$_GET[id_membre]'");
		$membre_actuel = $resultat -> fetch_assoc();
		//debug($membre_actuel);
		echo '<h1>Correction d\'un membre</h1>';
	}
	else{
		echo '<h1>Ajout d\'un membre</h1>';
	}

?>

	<!---------------- FORMULAIRE HTML ---------------------->
 

            <div class="container-fluid">

                <div class="row">
                    <form role="form" action="" method="post">
						<div class="col-lg-6">

							<input  type="hidden" name="id_membre" value="<?php if(isset($membre_actuel)){echo $membre_actuel['id_membre'];} else { echo '0'; }?>" />

                            <div class="form-group">
								<label>Pseudo</label>
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
									<input type="text" class="form-control" name="pseudo" placeholder="pseudo" value="<?php if(isset($membre_actuel)){echo $membre_actuel['pseudo'];}?>">
								</div>
							</div>

                            <div class="form-group">
								<label>Mot de passe</label>
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
									<input type="password" class="form-control" name="mdp" placeholder="password" value="<?php if(isset($membre_actuel)){echo $membre_actuel['mdp'];}?>">
								</div>
							</div>

                            <div class="form-group">
								<label>Nom</label>
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-pencil"></span></div>
									<input type="text" class="form-control" name="nom" placeholder="votre nom" value="<?php if(isset($membre_actuel)){echo $membre_actuel['nom'];}?>">
								</div>
							</div>

                            <div class="form-group">
								<label>Prénom</label>
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-pencil"></span></div>
									<input type="text" class="form-control" name="prenom" placeholder="votre prénom" value="<?php if(isset($membre_actuel)){echo $membre_actuel['prenom'];}?>">
								</div>
							</div>
							
						</div>		
						<div class="col-lg-6">

							<div class="form-group">
								<label>Email</label>
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></div>
									<input type="text" class="form-control" name="email" placeholder="votre email" value="<?php if(isset($membre_actuel)){echo $membre_actuel['email'];}?>"><br />
								</div>
							</div>

							<div class="form-group">
                                <label>Civilité</label>
								<select class="form-control" name="civilite" style="width:30%;">
									<option value="m" <?php if(isset($membre_actuel) && $membre_actuel['civilite'] == 'm'){echo 'selected';}?>  >Homme</option>
									<option value="f" <?php if(isset($membre_actuel) && $membre_actuel['civilite'] == 'f'){echo 'selected';}?>  >Femme</option>
								</select>                       
							</div>

							<div class="form-group">
                                <label>Statut</label>
								<select class="form-control" name="statut" style="width:30%;">
									<option value="0" <?php if(isset($membre_actuel) && $membre_actuel['statut'] == '0'){echo 'selected';}?>  >Membre</option>
									<option value="1" <?php if(isset($membre_actuel) && $membre_actuel['statut'] == '1'){echo 'selected';}?>  >Admin</option>
								</select>                       
							</div>
								
							<div class="form-group">
                                <label>Date enregistrement</label>
								<div class="input-group" id="date_enregistrement">
									<div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
								<input type='text' class="form-control" name="date_enregistrement" 
									value="<?php if(isset($membre_actuel)){
										$date_reformate = date("d/m/Y H:i:s", strtotime($membre_actuel['date_enregistrement']));
										echo $date_reformate;}?>" />
								</div>
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

