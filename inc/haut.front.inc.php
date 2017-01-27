<!DOCTYPE html>
<html lang="fr">

<head>

	<!-- Les balises meta-->
	<meta charset="UTF-8" />  

	<!-- pour les réseaux sociaux -->
	<!-- Twitter Card data -->
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@ericdubourg10">
	<meta name="twitter:title" content="Page Title">
	<meta name="twitter:description" content="Lokisalle | Location de salle pour vos meetings ! | lokisalle.com">
	<meta name="twitter:creator" content="@ericdubourg10">
	<!-- Twitter Summary card images must be at least 120x120px -->
	<meta name="twitter:image" content="http://www.example.com/image.jpg">

	<meta property="og:title" content="Lokisalle | Location de salle pour vos meetings ! | lokisalle.com" />
	<meta property="og:description" content="Lokisalle vous propose un service de location de salle pour l'organisation de vos réunions, séminaires, formations ou autres événements, que vous soyez particuliers ou professionnels." />
	<meta property="og:url" content="http://eric.dubourg1.free.fr/lokisalle" />
	<meta property="og:image" content="image.png" />
	<meta property="og:type" content="réservation de salles" />
   	<meta property="og:site_name"   content="Lokisalle by Eric Dubourg Studio" />
	<!-- fin pour les réseaux sociaux -->

	<!-- pour les smartphones et IE ancien -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- fin pour les smartphones -->

	<title>Lokisalle Réservation de salles - <?php echo $title; ?></title>	

	<link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
 
	<!-- Bootstrap Core CSS -->
    <link href="<?php echo RACINE_SITE;?>css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo RACINE_SITE;?>css/shop-homepage.css" rel="stylesheet">

	<!-- Fenêtre modale -->
	<link rel="stylesheet" href="<?php echo RACINE_SITE;?>css/jquery.modal.css" />
	
	<!-- CSS pour le site-->
    <link href="<?php echo RACINE_SITE;?>css/style.css" rel="stylesheet">

</head>

<body>

    <!-- Page Content -->
    <div class="container">

	<!-- Navigation -->
	<header> <!-- Pour l'inclusion de l'image de fond-->
    <h1>LOKISALLE Réservation de salles et de conférences</h1>
	</header>
		
	<nav class="menu" class="navbar navbar-inverse navbar-fixed-top" role="navigation">

		<div class="container" class="menuV">

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">

					<?php

					echo '<li><a class="'; if($title=="Index Front" ){echo 'current';} echo'" href="' . RACINE_SITE . 'index.php" >Lokisalle</a></li>';
					
					if (userConnecteAdmin()) {
						echo '<li><a class="'; if($title=="Index Back" ){echo 'current';} echo'" href="' . RACINE_SITE . 'admin/gestion_salles.php?action=affichage">Admin Back Office</a></li>';
					}
					
					//-----------------------------
					if (userConnecte()) {
						echo '<li><a class="'; if($title=="Profil" ){echo 'current';} echo'" href="' . RACINE_SITE . 'profil.php" >Profil</a></li>';
						echo '<li><a class="'; if($title=="Profil" ){echo 'current';} echo'" href="' . RACINE_SITE . 'connexion.php?action=deconnexion" >Déconnexion</a></li>';
						echo '<li><a class="'; if($title=="Accueil" ){echo 'current';} echo'" href="' . RACINE_SITE . 'accueil.php" >Accueil</a></li>';
						echo '<li><a href="#contact" rel="modal:open" class="'; if($title=="Contact" ){echo 'current';} echo'" href="' . RACINE_SITE . 'contact.php" >Contact</a></li>';
					}
					else {
						echo '<li><a href="#inscription" rel="modal:open" class="'; if($title=="Inscription" ){echo 'current';} echo'" href="' . RACINE_SITE . 'inscription.php" >Inscription</a></li>';
						echo '<li><a href="#connexion" rel="modal:open" class="'; if($title=="Connexion" ){echo 'current';} echo'" href="' . RACINE_SITE . 'connexion.php" >Connexion</a></li>';
						echo '<li><a class="'; if($title=="Accueil" ){echo 'current';} echo'" href="' . RACINE_SITE . 'accueil.php" >Accueil</a></li>';
						echo '<li><a href="#contact" rel="modal:open" class="'; if($title=="Contact" ){echo 'current';} echo'" href="' . RACINE_SITE . 'contact.php" >Contact</a></li>';
					}
					?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
		</div>
	</nav>







