<?php
require_once("inc/init.inc.php");
$title="Accueil";
require_once("inc/haut.front.inc.php");
require_once("inc/diapo.inc.php");
echo $msg;

?>

<!-- Le formulaire -->
    <div class="row">
        <div class="col-md-3">
			
			<div class="form-group">
				<h2 style="text-align:center;">Recherche Salle</h2>
			</div>
				
				<form>
		
					<div class="form-group">
						<label>Catégorie</label>
						<select class="form-control" id="categorie" onchange="recupPHP()">
							<option value="reunion" selected>reunion</option>
							<option value="bureau">bureau</option>
							<option value="formation">formation</option>
						</select>
					</div>

						<div class="form-group">
							<label>Ville</label>
							<select class="form-control" id="ville" onchange="recupPHP()">
                                <option value="Paris" selected>Paris</option>
                                <option value="Lyon">Lyon</option>
                                <option value="Marseille">Marseille</option>
							</select>
						</div>
					
						<div class="form-group">
							<label>Capacité</label>
							<select class="form-control" id="capacite" onchange="recupPHP()">
								<?php
								$i= 1;
								while ($i <= 100) {
									if ($i == 20)
										 { echo '<option value="' . $i . '" selected >' . $i . '</option>'; }
									else { echo '<option value="' . $i . '">' . $i . '</option>'; }
									$i++;
								} ?>
							</select>
						</div>
					
						<div class="form-group">
							<label>Prix</label>
							<input type="range" min="0" max="1500" step="5" name="prix" id="prix" oninput="document.getElementById('AfficheRange').textContent=value" onchange="recupPHP()"/>
							<span id="AfficheRange">0</span>
						</div>
					
						<div class="form-group">
							<label>Date d'arrivée</label>
							<div class="input-group">
								<div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
								<input type='text' class="form-control" name="date_arrivee" id="date_arrivee" onchange="recupPHP()" >
							</div>
						</div>

						<div class="form-group">
							<label>Date de départ</label>
							<div class="input-group">
								<div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
								<input type='text' class="form-control" name="date_depart" id="date_depart" onchange="recupPHP()" >
							</div>
						</div>

				</form>				
				
			</div>

    <div class="col-md-9" id="myDiv"></div>

	</div>	
		<script>
		// instanciation de l'objet XMLHttpRequest
		var ajax = new XMLHttpRequest();
		// https://developer.mozilla.org/fr/docs/Web/API/XMLHttpRequest
		
		
		// récupération des données PHP en utilisant notre objet ajax
		function recupPHP() {
			// on récupère la value du choix de l'utilisateur.
			var categorie = document.getElementById('categorie').value;
			var ville = document.getElementById('ville').value;
			var capacite = document.getElementById('capacite').value;
			var prix = document.getElementById('prix').value;
			var date_arrivee = document.getElementById('date_arrivee').value;
			var date_depart = document.getElementById('date_depart').value;
			

	function reponse(data) {
		document.getElementById('myDiv').innerHTML = data;
	}
	
	// déclaration de la fonction pour lancer l'ajax
	function requete(callbackReponse) {
		ajax.onreadystatechange = function () {
			if(ajax.readyState == 4 && ajax.status == 200)
			{
				callbackReponse(ajax.responseText);
			}
		}
	}
	
	// version POST
	var parameters="categorie="+categorie+"&ville="+ville+"&capacite="+capacite+"&prix="+prix+"&date_arrivee="+date_arrivee+"&date_depart="+date_depart;

	ajax.open("POST", "ajax_recup.php", true); // open(lamethode / urldufichieraexecuter / boolAsync) boolAsync => true (asynchrone) / false (synchrone) // vaut true par defaut 
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // obliger de préciser le header notamment en version POST
	ajax.send(req=parameters);

	// exécution des deux fonctions	
	requete(reponse); // l'argument reponse est la fonction reponse(). En revanche si nous marquons l'argument avec les () => requete(reponse()) dans ce cas l'argument est le résultat de la fonction. En mettant requete(reponse) dans ce cas nous récupérons le contenu de la fonction


			
			
		}
		</script>
	
<?php 


require_once("formulaires.php");
require_once("inc/datescriptjs.inc.php");
require_once("inc/bas.front.inc.php");

 


 ?>










