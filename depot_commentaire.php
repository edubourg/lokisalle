<?php
require_once("inc/init.inc.php");

$title="Dépôt Commentaire";
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg;

if ($_POST) {
	
	foreach ($_POST as $indice => $valeur) { // faille XSS
		$_POST[$indice] = htmlspecialchars($valeur);
	}

	foreach($_POST as $indice => $valeur){ // Injection SQL
		$_POST[$indice] = htmlentities(addslashes($valeur));
	}

	// Conversion int pour note
	$note = intval(substr($_POST['note'], 0, 2));

	// date de l'enregistrement
	req("REPLACE INTO avis (id_avis, id_membre, id_salle, commentaire, note, date_enregistrement) VALUES (0, '$_POST[id_membre]', '$_POST[id_salle]', '$_POST[commentaire]', '$note', NOW())");

	$msg .= '<p style="color: white; background-color: green; padding: 10px;">Le nouveau avis a été enregistré !';	
	
	}
	
	echo $msg;
	echo '<h4 class="pull-right"><a href="accueil.php">Retour vers le catalogue</a></h4>';


require_once("formulaires.php");
require_once("inc/bas.front.inc.php");
	
?>

