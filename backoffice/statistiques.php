<?php
require_once("../inc/init.inc.php");

if(!userConnecteAdmin()) {  
        header("location:../connexion.php");
    }

$title="Gestion avis";
require_once("../inc/haut.back.inc.php");
echo $msg;

?>

<!-- HTML -->
<div id="page-wrapper">

<?php
// Les requêtes

// top 5 des salles les mieux notées	
$salle_note = req("SELECT CONCAT (s.titre, ' ', s.id_salle) as Salle, avg(note) as Moyenne FROM salle s, avis a
					WHERE s.id_salle = a.id_salle GROUP BY 1 ORDER BY 2 DESC LIMIT 0,5");

// top 5 des salles les plus commandées
$salle_commande = req("SELECT DISTINCT CONCAT (s.titre, ' ', s.id_salle) as Salle FROM salle s, produit p 
					WHERE s.id_salle = p.id_salle AND p.etat = 'reservation' LIMIT 0,5");

// top 5 des membres qui achètent le plus (en termes de quantité)					
$membre_achat = req("SELECT DISTINCT pseudo, CONCAT (nom, ' ', prenom) AS personne, COUNT(id_commande) as Nb_commandes FROM membre m, commande c
					WHERE m.id_membre = c.id_membre GROUP BY 1, 2 ORDER BY 3 DESC LIMIT 0,5");

// top 5 des membres qui achètent le plus (en termes de prix)
$membre_cmd = req("SELECT DISTINCT pseudo, CONCAT (nom, ' ', prenom) AS personne, sum(p.prix) as max_prix FROM membre m, commande c, produit p
WHERE m.id_membre = c.id_membre AND p.id_produit = c.id_produit GROUP BY 1, 2 ORDER BY 3 DESC LIMIT 0, 5;");
					
// L'affichage des requêtes

?>

    <div class="container-fluid">

		<div class="col-lg-6">
			<?php affichage_requete ($salle_note, 'Top 5 des salles les mieux notées');					
			affichage_requete ($salle_commande, 'Top 5 des salles les plus commandées'); ?>					 

		</div>

		<div class="col-lg-6">

			<?php affichage_requete ($membre_achat, 'Top 5 des membres (en quantité)');					
			affichage_requete ($membre_cmd, 'Top 5 des membres (en prix)');	?>				

		</div>

	</div>


</div> <!-- /#page-wrapper -->

<?php
require_once("../inc/datescriptjs.php");
require_once("../inc/bas.back.inc.php");
?>

