<?php
require_once("inc/init.inc.php");


$title="Contact";
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg;

if ($_POST) {
	
	// Tests de vérification
	foreach ($_POST as $indice => $valeur) { // faille XSS
		$_POST[$indice] = htmlspecialchars($valeur);
	}

	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
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
	$_POST['email'] = str_replace(array("\n","\r",PHP_EOL),'',$email); // suppression du retour chariot adresse mail - faille CRLF

	if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
			$msg .=  '<p style="color: white; background-color: red; padding: 10px;">' . $_POST['email'] . 'n\'est pas une adresse email valide.</p>';
	}	

	if (strlen($_POST['message']) == 0) {
			$msg .= '<p style="color: white; background-color: red; padding: 10px;"> Attention vous devez saisir un message</p>';
	}
	
	if(empty($msg)){ 

		extract ($_POST);

		$expediteur = $nom . ' ' . $prenom;

		// Préparation du message
		$header = "From: $expediteur" . "\r\n";
		$header .= "Reply-To: $expediteur" . "\r\n";
		$header .= "MIME-Version: 1.0 \r\n";
		$header .= "Content-type: text/html; charset=iso-8859-1 \r\n";
		$header .= "X-Mailer: PHP/" . phpversion();

		$contenu_email = "<h1>Mail envoyé par $expediteur</h1>";
		$contenu_email .= "<p>$message</p>";

		$destinataire = "ericdubourg10@gmail.com";

		mail($destinataire, $sujet, $contenu_email, $header);
	
	}
	
}

echo $msg; 

require_once("formulaires.php");
require_once("inc/bas.front.inc.php");
	
?>

