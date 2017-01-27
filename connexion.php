<?php
require_once("inc/init.inc.php");
$title="Connexion";
require_once("inc/haut.front.inc.php");

// Si connect� : redirection vers profil
	if(userConnecte()){
		header('location: profil.php');	
}

//--- DECONNEXION
if(isset($_GET['action']) && $_GET['action'] == "deconnexion" ){
	unset($_SESSION['membre']);
	session_destroy();
	header('location: index.php');	
}

//--- CONNEXION
if($_POST){

	// Tests de v�rification
	// Pour parer aux failles CRLF
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
 
	if (empty($msg)) {
		/*if(!empty($_POST['pseudo'])){
			$_POST['pseudo'] = htmlentities(addslashes($_POST['pseudo']));
		}*/

		// Je v�rifie si le pseudo existe bien dans la Base de donn�es.
		$resultat = req("SELECT * FROM membre WHERE pseudo = '$_POST[pseudo]'");
	
		if($resultat -> num_rows != 0){ 
			$membre = $resultat -> fetch_assoc(); // Fetch_assoc() me permet de r�cup�rer les infos du membre et donc son MDP
			//debug($membre);
			
			if($membre['mdp'] == $_POST['mdp']){ // Si le MDP en BDD est �quivalent � celui transmis dans le formulaire
			
				// Je cr�e dans $_SESSION un ARRAY (tableau multidimensionnel) avec toutes les infos de l'utilisateur sauf le MDP (s�curit�) et donc l'utilisateur est connect� !! 
				foreach($membre as $indice => $valeur){
					if($indice != 'mdp'){
						$_SESSION['membre'][$indice] = $valeur; 
					}
				}
				// debug($_SESSION);
				header('location: profil.php');
			}
			else {
				$msg .= '<p style="color: white; background-color: red; padding: 10px;">Erreur de Mot de passe ! </p>';
			}
		}
		else{
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Erreur de pseudo ! </p>';
		}
	
		// Si connect� : redirection vers profil
		if(userConnecte()){
			header('location: profil.php');	
		}
	}
	
}

echo $msg;


// Formulaire de connexion
require_once("inc/diapo.inc.php");
require_once("formulaires.php");
require_once('inc/bas.front.inc.php');
?>