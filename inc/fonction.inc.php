<?php

//********************   FONCTIONS UTILISATEUR  *************************//

// Fonction pour executer des requêtes
function req($req){
	global $mysqli;
	$resultat = $mysqli -> query($req);
	if(!$resultat){
		die("Erreur sur la reqête SQL ! <br/> Message : " . $mysqli -> error . "<br/> Requête : " . $req);	
	}
	return $resultat;
}
//---------------------------
// Fonction debug pour les print_r et var_dump
function debug($var, $mode = 1){
	echo '<div style="background-color: #' . rand(111111, 999999) . '; padding : 5px; margin: 5px; color: white" >';
	if($mode === 1){
		echo '<pre>'; print_r($var); echo '</pre>';
	}
	else{
		echo '<pre>'; var_dump($var); echo '</pre>';
	}
	echo '</div>';
}
//----------------------------
function userConnecte(){
	if(!isset($_SESSION['membre'])){ // Si la session "membre" n'est pas définie (elle ne peut être définie que si nous passé par la page connexion.)
		return FALSE; 
	}
	else{
		return TRUE; 
	}	
}
//----------------------------
function userConnecteAdmin(){
	if(userConnecte() && $_SESSION['membre']['statut'] == 1){ // Si la session membre est définie, nous vérifions si l'utilisateur est admin
		return TRUE; 
	}
	else{
		return FALSE; 
	}
}
//----------------------------
// Création du panier
function creationPanier(){
	if(!isset($_SESSION['panier'])){
		$_SESSION['panier']= array();
		$_SESSION['panier']['titre']= array();
		$_SESSION['panier']['id_produit']= array();
		$_SESSION['panier']['quantite']= array();
		$_SESSION['panier']['prix']= array();
	}
}


//----------------------------
// AJOUTER UN PRODUIT DANS LE PANIER
function ajoutProduitPanier($titre, $id_produit, $quantite, $prix){
	creationPanier();
	$position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']); //ARRAY SEARCH me permet de chercher un élément dans un array et retourne la position de cet élément s'il existe ou false s'il n'existe pas dans l'array. 
	//1er argument l'élément que je recherche
	//2eme argument : l'Array dans lequel je cherche 
	
	if($position_produit !== FALSE) { //le produit existe dans le panier
		$_SESSION['panier']['quantite'][$position_produit] += $quantite;	
		//... donc j'ajoute la nouvelle commande à la quantité de ce produit dans le panier. Je récupère la position de ce roduit dans le panier grâce à $position_produit.
	}
	else{ // Le produit n'existe aps encore dans le panier, donc je le crée. Les crochets vides me permettent d'ajouter à la suite.
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['prix'][] = $prix;
		$_SESSION['panier']['id_produit'][] = $id_produit;
	}
}

//-------------------------- 
// CALCULER LE TOTAL
function montantTotal(){
	$total = 0; // Je crée un evariable qui va être incrémentée du prix des différents produits
	for($i = 0; $i < sizeof($_SESSION['panier']['id_produit']); $i++){
		// Tant qu'il y a des produits dans le panier, je multiplie la quantité et le prix et j'ajoute à la variable TOTAL. 
		$total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
	}
	return round($total, 2); // J'arrondie le total à 2 chiffres après la virgule. 
}

//--------------------
//RETIRER UN PRODUIT DU PANIER
function retirerProduitPanier($id_produit_a_supprimer){
	$position_produit = array_search($id_produit_a_supprimer, $_SESSION['panier']['id_produit']); 
	// Je vérifie dans un  premier que le produit est bien présent dans le panier. Si oui je récupère sa position. 
	
	if($position_produit !== FALSE){ // Si le produit est présent dans le panier, grâce à array_splice je supprime chaque éléments qui correspondent à ce produit. 
	// Array_splice attend 3 arg : 
	// 1 : L'array
	// 2 : L'élément à supprimer
	// 3 : Le nombre d'élément à supprimer
		array_splice($_SESSION['panier']['quantite'], $position_produit, 1);
		array_splice($_SESSION['panier']['prix'], $position_produit, 1);
		array_splice($_SESSION['panier']['titre'], $position_produit, 1);
		array_splice($_SESSION['panier']['id_produit'], $position_produit, 1);
	}
}

 // Conversion de date anglais -> français
function convertDate($date)
{
	$tabDate = explode('/' , $date); 
		
	//debug($tabDate);
	$timeDate = explode(' ', $tabDate[2]);
	//debug($timeDate);
	
	$enDate  = $timeDate[0].'-'.$tabDate[1].'-'.$tabDate[0] . ' ' . $timeDate[1];
     return $enDate;
}

 // Conversion de date français -> anglais JJ/MM/AAAA HH:MM devient AAAA-MM-JJ HH:MM
function convertDateEn($date)
{
     $tabDate = explode('/' , $date);
	//debug($tabDate);
	$timeDate = explode(' ', $tabDate[2]);
	//debug($timeDate);
	
	$enDate  = $timeDate[0].'-'.$tabDate[1].'-'.$tabDate[0] . ' ' . $timeDate[1];

    // $enDate  = $tabDate[2].'-'.$tabDate[1].'-'.$tabDate[0];
     return $enDate;
}

// Calcul de la note
function calcul_note($indice_salle) {

	$resultat = req("SELECT s.titre, round(avg(a.note), 2) as note_moyenne FROM salle s LEFT JOIN avis a 
						ON s.id_salle = a.id_salle AND s.id_salle = " . $indice_salle);
					
	$ligne = $resultat->fetch_assoc();
		
	//Notation
	if (isset($ligne['note_moyenne'])) {
				
	$note = intval($ligne['note_moyenne']);
	$note_avis = 5;
					
	if ($note < 16) { $note_avis--; }
	if ($note < 12) { $note_avis--; }
	if ($note < 8) { $note_avis--; }
	if ($note < 4) { $note_avis--; }
				
	}
	else
	{
		$note_avis = 0;
	}

	return $note_avis;
	
}
	
// Affichage requetes statistiques
function affichage_requete ($resultat, $titre)
{
	?>

		<h2><?php echo $titre ?></h2>
			<div class="table-responsive">
				<table class="table table-bordered table-hover table-striped">
				<thead>
				<tr>
					<?php while($colonne = $resultat -> fetch_field()){
						//if ($colonne->name == 'id_salle') {
							echo '<th>' . $colonne -> name . '</th>';	
						//} 
					} ?>
				</tr>
				</thead>

				<?php $i = 1;
				while($ligne = $resultat -> fetch_assoc()){
					echo '<tr>';
					foreach($ligne as $indice => $valeur){
						switch ($indice) {
							
							case 'Salle':
								echo '<td>' . $i . ' - ' . $indice . ' ' . $valeur . '</td>';
								$i++;
								break;
							
							case 'Moyenne':
								echo '<td>' . round($valeur, 2) . '</td>';
								break;
								
							default: 
								echo '<td>' . $valeur . '</td>';
								break;

						}
					}		
			echo '</tr>';
			}
			echo "</table>";
		?>
				
			</div>
<?php
	
}


