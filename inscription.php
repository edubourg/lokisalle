<?php
require_once("inc/init.inc.php"); 

// Si connecté : redirection vers profil
if(userConnecte()){
	header('location: profil.php');	
}

if($_POST){
	//debug($_POST);

	// Tests de vérification
	
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
	
	if(empty($msg)){ 
		
		// Vérification de la disponibilité du pseudo : 
		$membre = req("SELECT * FROM membre WHERE pseudo='$_POST[pseudo]'");
		if($membre -> num_rows > 0){ 
			$msg .= '<p style="color: white; background-color: red; padding: 10px;">Pseudo indisponible ! Veuillez choisir un autre pseudo.</p>';
		}
		else{ // L'utilisateur a un pseudo unique
			foreach($_POST as $indice => $valeur){
				$_POST[$indice] = htmlentities(addslashes($valeur));
			}
			
			// Requete d'insertion de l'utilisateur dans la BDD
			req("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite,  statut, date_enregistrement) VALUES ('$_POST[pseudo]', '$_POST[mdp]', '$_POST[nom]', '$_POST[prenom]', '$_POST[email]', '$_POST[civilite]', '0', NOW())");
		
			//Message de félicitations
			$msg .= '<div class="validation">Félicitions ' . $_POST['pseudo'] . ', vous êtes inscrit à notre site ! <a href="connexion.php">Se connecter</a></div>';
			
			//Redirection
			header('location: connexion.php');	
		}
	}
}
$title = 'Inscription'; 
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg; 

// Formulaire de connexion
require_once("formulaires.php");
require_once("inc/bas.front.inc.php");
?>