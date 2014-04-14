<?php
session_start(); 
require('/var/www/resa/dev/config.php'); 
require('fonctions.php'); 
?> 
<html dir="ltr" lang="fr-FR">

<head>

	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	
	<link rel="stylesheet" href="includes/css/foundation.css">
<!--	<link rel="stylesheet" href="includes/css/datePicker.css">-->
<link rel="stylesheet" href="includes/css/calendrier.css">
	<link rel="stylesheet" href="includes/foundation_icons_general/stylesheets/general_foundicons.css">
	
<!--	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">-->
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script src="scripts/jquery.ui.datepicker-fr.js"></script>

	<script src="scripts/js/vendor/modernizr.js"></script>
	<script src="scripts/js/vendor/fastclick.js"></script>
	<script src="scripts/js/vendor/jquery.js"></script>
	<script src="scripts/js/foundation/foundation.js"></script>
	<script src="scripts/js/foundation/foundation.topbar.js"></script>
	<script src="scripts/js/foundation/foundation.tab.js"></script>

	<style>

			a.dp-choose-date {
				float: left;
				width: 16px;
				height: 16px;
				padding: 0;
				margin: 5px 3px 0;
				display: block;
				text-indent: -2000px;
				overflow: hidden;
				background: url(calendar.png) no-repeat; 
			}
			a.dp-choose-date.dp-disabled {
				background-position: 0 -20px;
				cursor: default;
			}
			/* makes the input field shorter once the date picker code
			 * has run (to allow space for the calendar icon
			 */
			input.dp-applied {
				width: 140px;
				float: left;
			}
	</style>

</head>

<body>

<div class="row">
	<div class="contain-to-grid fixed">
		<nav class="top-bar" data-topbar>
		  <ul class="title-area">
			<li class="name">
			  <h1><a href="affichTous.php">Calendrier</a></h1>
			</li>
			<li class="toggle-topbar menu-icon"><a href="#">menu</a></li>
		  </ul>

		  <section class="top-bar-section">
			<!-- Right Nav Section -->
			<ul class="right">
			  <li class="has-dropdown">
				<a href="statistique.php">Statistiques</a>
				<ul class="dropdown">
				  <li><a href="#">Reservation</a></li>
				  <li><a href="#">CA</a></li>
				  <li><a href="#">CA</a></li>
				  <li><a href="#">CA</a></li>
				</ul>
			  </li>
			  <li class="has-dropdown">
				<a href="#">Marketing</a>
				<ul class="dropdown">
				  <li><a href="facturation.php">Facturation</a></li>
				  <li><a href="mailing.php">Mailing</a></li>
				  <li><a href="reseauxSociaux.php">Réseaux Sociaux</a></li>
				</ul>
			  </li>
			</ul>

			<!-- Left Nav Section -->
			<ul class="left">
			 <li class="has-dropdown">
				<a href="#">Gîtes</a>
				<ul class="dropdown">
					<li><a href="calendrier.php?idgite=1">Tout le centre</a></li>
					<li><a href="calendrier.php?idgite=2">Gite 1</a></li>
					<li><a href="calendrier.php?idgite=3">Gite 2</a></li>
					<li><a href="calendrier.php?idgite=4">Gite 3</a></li>
					<li><a href="calendrier.php?idgite=5">Gite 4</a></li>
					<li><a href="calendrier.php?idgite=6">Gite 5</a></li>
					<li><a href="calendrier.php?idgite=7">Gite 6</a></li>
					<li><a href="calendrier.php?idgite=8">Dortoir</a></li>
				</ul>
			</ul>
			<ul class="left">
			 <li class="has-dropdown">
				<a href="#">Recherche</a>
				<ul class="dropdown">
				  <li><a href="rechercheClient.php">Clients</a></li> 
				  <li><a href="rechercheCommande.php?actionCommande=Z">Commande</a></li>
				</ul>
			</ul>
			<ul class="left">
			 <li class="has-dropdown">
				<a href="#">Gestion Gîte</a>
				<ul class="dropdown">
				  <li><a href="affichGite.php">Gestion gîtes</a></li> 
				  <li><a href="affichOptions.php">Gestion options</a></li>
				  <li><a href="affichSaisons.php">Gestion saisons</a></li>
				  <li><a href="affichCodepromo.php">Gestion code promotion</a></li> 
				  <li><a href="affichTaxe.php">Gestion taxe</a></li>
				</ul>
			</ul>
		  </section>
		</nav>
	</div>
</div>
