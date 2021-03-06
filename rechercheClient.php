<?php

//recherche de clients des gites 
require('includes/header.php');

	
$MessageAction=""; // permet d'afficher un message de confirmation ou d erreur lors d'une action sur la BD
/*********************************************
*		REcherche des clients  des gites     *
**********************************************/		
		
	/**
		* note EVM (code pour dev envoi mail)
	*/
		
    /** 
			* differentes valeurs de la variable actionClient passee en argument 
			* vide : on affiche un formulaire de recherche de clients
			* R : on affiche les r�sultat
				* MS : update info clinet
				* D : suppresion client
				* A : ajout client
			* TE : trie mail
			* TN : trie nom
			* TP : trie prenom
			* MDP : creation nouveau mot de passe
			* EM : envoi new mp par mail
			* MAJ : afichage formulaire modification
			* CR : formulaire création client 
	*/
	
	/***
	
		* recherche client en fonction d'une commande (de la page recherche commande)
		
	*/
	if (!empty($_GET["idcommande"])) {
		$idcommande=$_GET["idcommande"];
		
		$req_IdClientCommande = "SELECT idclient FROM COMMANDERESERVER WHERE idcommande='".$idcommande."'";
		$result_IdClientCommande=$mysqli->query($req_IdClientCommande);
		
		while($row_IdClientCommande = $result_IdClientCommande->fetch_assoc()) {
			
			$idclient=$row_IdClientCommande['idclient'];
		}
	}

	/**
		* édition client sur page client 
	*/
	
	if (!empty($_GET["actionClient"]))
	{
		$actionClient=$_GET["actionClient"];
		$editionClient=$_GET["editionClient"];

		//on regarde si on a un idclient en parametre
		if (!empty($_GET["idclient"])) {
			$idclient=$_GET["idclient"];
		}
		
		if(!empty($_GET['email'])) {
			$email=$_GET['email'];
		} else {
			$email=$_POST['email'];
		}
		
		if($editionClient=='MS') {
			
			$reqUpdate="update CLIENTS	
					 SET nom='".$_POST["nom"]."', prenom='".$_POST["prenom"]."', entreprise='".$_POST["entreprise"]."', 
					 adresse='".$_POST["adresse"]."', codepostal='".$_POST["codepostal"]."', ville='".$_POST["ville"]."', 
					 pays='".$_POST["pays"]."', tel='".$_POST["tel"]."', port='".$_POST["port"]."', 
					 email='".$_POST["email"]."',date_naissance='".$_POST["date_naissance"]."', 
					 cheminot='".$_POST["cheminot"]."', code_cheminot='".$_POST["code_cheminot"]."', region='".$_POST["region"]."', 
					 newsletter='".$_POST["newsletter"]."', 
					 commentaire='".$_POST["commentaire"]."'
					 where idclient= ".$idclient ;
				$mysqli->query($reqUpdate);
				
			if($mysqli) {
				$MessageAction="Les information du client ont &eacute;t&eacute; mise à jour";
				if ($_POST["cheminot"] == 1){
					$messageCheminot = "Cheminot : Oui
						Code cheminot : ".$_POST["code_cheminot"]."
						Region Alsace : ";
					if ($_POST["region"] == 1)
						$messageCheminot .= "Oui";
					else
						$messageCheminot .= "Non";
				}
				else 
					$messageCheminot = "Cheminot : Non";
				$messageMailModif = "Nous vous informons que votre compte a &eacute;t&eacute; modifi&eacute;.Voici un rappel de vos informations :
				N° Client : $idclient
				Nom : ".$_POST["nom"]."
				Prenom : ".$_POST["prenom"]."
				Entreprise : ".$_POST["entreprise"]."
				Adresse : ".$_POST["adresse"]."
				Code postal : ".$_POST["codepostal"]."
				Ville : ".$_POST["ville"]."
				Pays : ".$_POST["pays"]."
				Telephone : ".$_POST["tel"]."
				Port : ".$_POST["port"]."
				Date de naissance : ".$_POST["date_naissance"]."
				$messageCheminot
				Newsletter : ";
				if ($_POST["newsletter"] == 1)
					$messageMailModif .= "Oui";
				else 
					$messageMailModif .= "Non";
				envoyerEmail($_POST["email"], "Vos inforamtions ont &eacute;t&eacute; modifi&eacute;es", $messageMailModif);
			}
			else {
				$MessageAction="Probl&egrave;me lors de la mise &agrave; jour";
			}
		
		
		}
		else if ($editionClient=='A') {
		
				$email=strtolower($_POST["email"]); // recuperation email
				if (empty($email))
				$email = "aucun";
				
				$newPass = chaineAleatoire(8); // creation d'un mot de passe
			
				$pass          = md5($newPass) ;
				$date_creation = date("Y-m-d H:i:s"); 

				/* insertion dans la base*/
				$reqInsertClient="INSERT INTO CLIENTS (email,mp,nom,prenom,date_naissance,cheminot,code_cheminot,region,entreprise,adresse,codepostal,ville,pays,tel,port,creation,newsletter) VALUES ('".$email."','".$newPass."','".$_POST["nom"]."','".$_POST["prenom"]."','".$_POST["date_naissance"]."','".$_POST["cheminot"]."','".$_POST["region"]."','".$_POST["code_cheminot"]."','".$_POST["entreprise"]."','".$_POST["adresse"]."','".$_POST["codepostal"]."','".$_POST["ville"]."','".$_POST["pays"]."','".$_POST["tel"]."','".$_POST["port"]."','".$date_creation."','".$_POST["newsletter"]."','".$_POST["commentaire"]."')";;
				$mysqli->query($reqInsertClient);

			if($mysqli) {
				$MessageAction="Le client a &eacute;t&eacute; cr&eacute;e";
				envoyerEmail($email, "Votre compte a bien &eacute;t&eacute; cr&eacute;&eacute;", "Nous vous informons que votre compte a &eacute;t&eacute; cr&eacute;&eacute; avec succ&egrave;s.\nVotre mot de passe : ".$newPass);
				//envoiMail($email, "Votre nouveau mot de passe","voici votre mot de passe : ".$newPass,$copy); // envoi par email (recap client +mp)
			}
			else {
				$MessageAction="Probl&egrave;me lors de la cr&eacute;ation du client";
			}
		
		}
		else if($editionClient=='D') {

				$req_TestCommande = "SELECT idcommande FROM COMMANDERESERVER WHERE idclient='".$idclient."'";

				$result_TestCommande=$mysqli->query($req_TestCommande);
				if(!$result_TestCommande) {
					$avertissementSuppression="Impossible de supprimer le client, supprimer d'abord les commandes en cours de ce client.";
				}
				else {
					
					/**
	
						!!* EVM envoi mail + template!!
					*/
					
					$suppClient="DELETE FROM CLIENTS WHERE idclient='".$idclient."'";
					
					$mysqli->query($suppClient);
					
					if($mysqli) {
						$MessageAction="Le client a &eacute;t&eacute; supprim&eacute;";
					}
					else {
						$MessageAction="Erreur lors de la suppression du client";
					}
				}
		}
		switch ($actionClient) 
		{
			case "R": //rechercher de clients
				
				$reqClient="SELECT idclient, nom, prenom, port, email	
						FROM CLIENTS";

					if(!empty($_GET["idcommande"]) || !empty($_GET["idclient"])) {
						 	 
						 $reqClient.=" WHERE idclient='".$idclient."'";		 
					}
					else {
						 
						if (!empty($email) and (!empty($_POST["nom"])))
						{
							$reqClient.=" where email like '".$_POST["email"]."' and nom like '%".$_POST["nom"]."%'";		 
						}
						 else
						{
							if (!empty($_POST["nom"]) ) 					 
							{
								$reqClient.=" where nom like '".$_POST["nom"]."'";
							}
							else
							{
								if (!empty($email))
								{
									$reqClient.=" where email like '".$email."'";
								}
								else
								{
									if (!empty($_POST["port"]))  $reqClient.=" where port like '".$_POST["port"]."'";
								}
							}
						}
						 
					}
			 
				$result_reqClient=$mysqli->query($reqClient);
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r&eacute;sultat pour cette recherche" ;  
				} 
				else
				{
					$MessageAction="R&eacute;sultat de la recherche : ";
				}
							
				//Boucle qui parcourt les clients dans la base de donn�es
				
			
				break;
						
			case "TE": //tri par email
				$reqClient="SELECT idclient, nom, prenom, port, email	
						FROM CLIENTS ORDER BY email";
											 
				$result_reqClient=$mysqli->query($reqClient);
				break;
			case "TN": //tri par nom
				$reqClient="SELECT idclient, nom, prenom, port, email	
						FROM CLIENTS ORDER BY nom"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "TP": //tri par prenom
				$reqClient="select idclient, nom, prenom, port, email	
						from CLIENTS order by prenom"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "TT": //tri par portable
				$reqClient="SELECT idclient, nom, prenom, port, email	
						FROM CLIENTS ORDER BY port"
						 ;
											 
				$result_reqClient=$mysqli->query($reqClient);		 
				break;
			case "MDP": //generation d'un nouveau mot de passe
				$newPass=envoiPwd($_GET["email"]);
				$MessageAction='<div class="messageInfo">Le nouveau mot de passe est : '.$newPass.'</div>';
				// $reqClient="select idclient, nom, prenom, port, email	
				// 		from CLIENTS where email='".$_GET["email"]."'";
				// 		$result_reqClient=$mysqli->query($reqClient);
				envoyerEmail($_GET["email"], "Votre nouveau mot de passe","Voici votre nouveau mot de passe : ".$newPass);
				break;
			case "EM": //generation d'un nouveau mot de passe
				if (envoiMail($_GET["email"],"mon objet","dfdf",true))
				 { echo "envoi ok";}
				/**
				* innutilisé car fait en 
				*/
				break;
				
			case "MAJ": //génération du formulaire de modifications
				
				$idclient=$_GET['idclient'];
				
				// construction de la requete
				$reqClient="SELECT idclient, nom, prenom, entreprise, adresse, codepostal, ville, pays, tel, port, email,date_naissance, creation, cheminot, code_cheminot, region, newsletter, commentaire 
									FROM CLIENTS
									WHERE idclient=".$idclient;
									
				$result_reqClient=$mysqli->query($reqClient);
				
				if(!$mysqli)
				{
					$MessageAction ="ERREUR : Pas de r&eacute;sultat " ;  
				} 
				else
				{
					$MessageAction="Client ok : ";
				}
							
				while ($row = $result_reqClient->fetch_assoc())
				{			
				$affichage_info_client='
				<form action="rechercheClient.php?actionClient=R&editionClient=MS&idclient='.$idclient.'" method="post">
					<ul>';
				$affichage_info_client.='
						<li>
							<label for="nom">Nom : 
								<input id="nom" name="nom" type="text" value="'.$row["nom"].'"'  .$modif. '>
							</label></li>
						<li>
							<label for="prenom">Pr&eacute;nom : 
								<input id="prenom" name="prenom" type="text" value="'.$row["prenom"].'"'.$modif.'>
							</label>
						</li>
						<li>
							<label for="entreprise">Entreprise : 
								<input id="entreprise" name="entreprise" type="text" value="'.$row["entreprise"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="adresse">Adresse : 
								<input id="Adresse" name="Adresse" type="text" value="'.$row["Adresse"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="codepostal">Codepostal : 
								<input id="codepostal" name="codepostal" type="text" value="'.$row["codepostal"].'"'  .$modif. '>
							</label>							</li>
						<li>
							<label for="ville">Ville :
								<input id="ville" name="ville" type="text" value="'.$row["ville"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="pays">Pays : 
								<input id="pays" name="pays" type="text" value="'.$row["pays"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="tel">T&eacute;l&eacute;phone : 
								<input id="tel" name="tel" type="text" value="'.$row["tel"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="port">Portable : 
								<input id="port" name="port" type="text" value="'.$row["port"].'"'  .$modif. '>
							</label>
						</li>
						<li><label for="email">Email : 
								<input id="email" name="email" type="text" value="'.$row["email"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="date_naissance">Date naissance : 
								<input id="date_naissance" name="date_naissance" type="date" value="'.$row["date_naissance"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="creation">Date cr&eacute;ation: 
								<input id="creation" name="creation" type="text" value="'.date_format(date_create($row["creation"]),'d-m-Y H:i:s').'"'  .$modif. '>
							</label>
						</li>
						
						<li>
							<label for="cheminot">Cheminot : 
								<SELECT name="cheminot">';
								if (((int)$row["cheminot"])==1) 
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["cheminot"].'">Oui</option>
															<option  value="0">Non</option>';
								}
								else							
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["statut"].'">Non</option>
															<option  value="1">Oui</option>';
								}
								$affichage_info_client.='</select>
							</label>
						</li>
						
						<li>
							<label for="code_cheminot">N&deg; CP : 
								<input id="code_cheminot" name="code_cheminot" type="text" value="'.$row["code_cheminot"].'"'  .$modif. '>
							</label>
						</li>
						<li>
							<label for="region">R&eacute;gion : 
								<select name="region">';
							
								if (((int)$row["region"])==1) 
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["region"].'">Oui</option>
															<option  value="0">Non</option>';
								}
								else							
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["region"].'">Non</option>
															<option  value="1">Oui</option>';
								}
								$affichage_info_client.='
								</select>
							</label>
						</li>
					
						<li>
							<label for="newsletter">Newsletters ';
					
								$affichage_info_client.='<select name="newsletter">';
								if (((int)$row["newsletter"])==1) 
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["newsletter"].'">Oui</option>
															<option  value="0">Non</option>';
								}
								else							
								{
									$affichage_info_client.='<option selected="selected" value="'.$row["newsletter"].'">Non</option>
															<option  value="Oui">Oui</option>';
								}
								$affichage_info_client.='
								</select>
							</label>
						</li>
					<li>
						<label for="commentaire">Commentaire 
							<input id="commentaire" name="commentaire" type="text" value="'.$row["commentaire"].'"'  .$modif. '>
						</label>
					</li>
					<li>
						<input class="button" type="submit" value="Modifier">
					</li>
				</form>
			</ul>';	
			}
						
				break;
				
			case "CR":
				$affichage_info_client='
					<form action="rechercheClient.php?actionClient=R&editionClient=A" method="POST">';
					$affichage_info_client.='
							<label for="nom">Nom : 
								<input id="nom" name="nom" type="text"'  .$modif. '>
							</label></li>
							<label for="prenom">Pr&eacute;nom : 
								<input id="prenom" name="prenom" type="text"'.$modif.'>
							</label>
							<label for="entreprise">Entreprise : 
								<input id="entreprise" name="entreprise" type="text"'  .$modif. '>
							</label>
							<label for="adresse">Adresse : 
								<input id="Adresse" name="Adresse" type="text"'  .$modif. '>
							</label>
							<label for="codepostal">Codepostal : 
								<input id="codepostal" name="codepostal" type="text"'  .$modif. '>
							</label>							</li>
							<label for="ville">Ville :
								<input id="ville" name="ville" type="text"'  .$modif. '>
							</label>
							<label for="pays">Pays : 
								<input id="pays" name="pays" type="text"'  .$modif. '>
							</label>
							<label for="tel">T&eacute;l&eacute;phone : 
								<input id="tel" name="tel" type="text"'  .$modif. '>
							</label>
							<label for="port">Portable : 
								<input id="port" name="port" type="text"'  .$modif. '>
							</label>
							<label for="email">Email : 
								<input id="email" name="email" type="text" placeholder="Laissez ce champ vide si le client n\'a pas d\'adresse email"'  .$modif. '>
							</label>
							<label for="date_naissance">Date naissance : 
								<input id="date_naissance" name="date_naissance" type="date"'  .$modif. '>
							</label>
							<label for="cheminot">Cheminot : 
								<select name="cheminot">
									<option value="1">Oui</option>
									<option selected="selected" value="0">Non</option>
								</select>
							</label>
							<select name="ce_cheminot" id="ce_cheminot"> ';
							$reqCe = "SELECT idce, nom_ce, reduction FROM CELISTE";
							echo $reqCe;
							$sqlCe=$mysqli->query($reqCe);

							while ($resqlCe=$sqlCe->fetch_Assoc()) 
							{
								$affichage_info_client.='<option value='.$reduction=$resqlCe['reduction'].'>'.$nom_ce=$resqlCe['nom_ce'].'</option>';	
							}

							$affichage_info_client.='</select><label for="code_cheminot">N&deg; CP : 
								<input id="code_cheminot" name="code_cheminot" type="text"' . $modif . '>
							</label>
							<label for="region">R&eacute;gion : 
								<select name="region">
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</label>
							<label for="newsletter">Newsletters
								<select name="newsletter">
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</label>
							<label for="commentaire">Commentaire 
								<input id="commentaire" name="commentaire" type="text"'  .$modif. '>
							</label>
							<input calss="button" type="submit" value="Cr&eacute;er">
					</form>';		
				break;	
	}
	if ((strcmp($actionClient,'R')==0) or (strcmp($actionClient,'TE')==0) or (strcmp($actionClient,'TN')==0) or (strcmp($actionClient,'TP')==0) or (strcmp($actionClient,'TT')==0) or (strcmp($actionClient,'MDP')==0) or (strcmp($actionClient,'MAJ')==0))
		{
			if ((strcmp($actionClient,'R')==0) and (strcmp($editionClient,'D')==0))
			{

				$MessageAction = "Le client a &eacute;t&eacute; supprim&eacute;";
			}
			else {

			// Creation du tableau pour afficher les clients
					$affichage_client_ligne='<table border="2"  rules="groups" id="tableauClient" class="rechClient" width="600"><thead>
									<tr><td ><a href="rechercheClient?actionClient=TE">Email</a></td><td><a href="rechercheClient?actionClient=TN">Nom</a></td><td><a href="rechercheClient?actionClient=TP">Pr&eacute;nom</a></td><td><a href="rechercheClient?actionClient=TT">Portable</A></td><th colspan="6">Action</th></tr>
									</thead>';
				//boucle qui parcourt le r�sultats des requetes demand�es dans la BD
				while ($row = $result_reqClient->fetch_assoc())
				{
					$affichage_client_ligne.= '<tr>
											<td><a href="mailto:'.$row["email"].'">'.$row["email"].'</td>
											<td>'.$row["nom"].'</td>
											<td>'.$row["prenom"].'</td>
											<td>'.$row["port"].'</td>
											<td><a href="affichTous.php?idClient='.$row["idclient"].'"><i class="foundicon-calendar"></i></a></td>
											<td><a href="rechercheClient.php?actionClient=MAJ&idclient='.$row["idclient"].'&email='.$row["email"].'" title="Modifier les informations du client"><i class="foundicon-edit"></i></a></td>
											<td><a onclick="apercuMail(\''.$row["email"].'\',\''.ucfirst($row["civilite"]).'. '.ucfirst($row["prenom"]).' '.ucfirst($row["nom"]).'\');" title="Envoyer un mail"><i class="foundicon-mail"></i></a></td>'
											//<td><a href="rechercheClient.php?actionClient=EM&email='.$row["email"].'" title="Envoyer un email"><i class="foundicon-mail"></i></a></td>
											.'<td><a href="rechercheClient.php?actionClient=MDP&email='.$row["email"].'" title="Generer un nouveau Mot de passe" onclick="return confirm(\'&Ecirc;tes vous sur(e) de vouloir reg&eacute;n&eacute;rer un mot de passe?\');"><i class="foundicon-lock"></i></a></td>
											<td><a href="rechercheCommande.php?actionCommande=R&email='.$row["email"].'" title="Voir les commandes du client"><i class="foundicon-cart"></i></a></td> 
											<td><a href="rechercheClient.php?actionClient=R&editionClient=D&idclient='.$row["idclient"].'" title="Supprimer ce client" onclick="return confirm(\'&Ecirc;tes vous sure de vouloir supprimer le client\');"><i class="foundicon-remove"></i></a></td>
											</tr>'; 
				}		

			$affichage_client_ligne.='</table>';	
			}
		}
	}
	
if (!empty($MessageAction))
{
	$MessageAction='<div class="messageInfo">'.$MessageAction.'</div>';
}
/*************************************************
*												 *
*	affichages des clients stock�es dans la base *
*												 *	
**************************************************/

$affichage_recherche='<p>Vous pouvez remplacer des carct&egrave;res inconnus par % pour effectuer la recherche</p>';
$affichage_recherche.='<form action="rechercheClient.php?actionClient=R" method="post">';
$affichage_recherche.='<label for="email">Email : </label><input id="email" name="email" type="text">
			<label for="nom">Nom : </label><input id="nom" name="nom" type="text">
			<label for="port">Num�ro de portable: </label><input id="port" name="port" type="int">';
$affichage_recherche.='<input type="submit" class="button tiny" value="Rechercher"></form>';
$bouton_creation ='<a href="rechercheClient.php?actionClient=CR" class="button tiny">Cr&eacute;ation d\'un client</a>';


?>
<!-- Modal d'apercu du mail -->
<div id="modalApercuEmail" class="reveal-modal" data-reveal>
	<div>
		<?php
		if ( $_GET["actionClient"] == 'R' ){
			require_once 'includes/ink/baseMailHTML.php';
			echo //$messageCSS.
			$messageBodyBefore.'<input type="text" id="sujetMail" placeholder="Sujet">
			<tr>
				<td>
					<h1 id="titreMail"></h1>
					<form>
						<input id="hiddenEmail" type="hidden" name="email" value="">
						<textarea rows="10" id="contenumail" name="message">Votre texte ici.</textarea>
					</form>
					<p>Pour toute question vous pouvez contacter le g&icirc;te.</p>
					<p>&Agrave; tr&egrave;s bient&ocirc;t pour d&eacute;couvrir notre magnifique r&eacute;gion</p>
				</td>
				<td class="expander"></td>
			</tr>'.$messageBodyAfter.'
			<button class="tiny" onclick="envoi()" >Envoyer</button>';
		}
		?>
	</div>
	<a class="close-reveal-modal">&#215;</a>
</div>
<!-- fin du Modal d'apercu du mail -->
<div class="row">
	<div class="large-12 columns">
		<div class="panel">
			<h2>Message</h2>
				<?= $affichage_recherche; ?>
				<?= $bouton_creation; ?> 
				<?= $MessageAction; ?>
				<?= $avertissementSuppression?>
			
		</div>
	</div>
</div>

<div class="row">
	<div class="large-12 columns">
		<div class="panel">
			<h2>Affichage Clients</h2>
			<?= $affichage_client_ligne;?>
			<?= $affichage_info_client;?>
		</div>
	</div>		
</div>
<?php
	require('includes/footer.php');
?>
<script>
function apercuMail(emailAdd, nomClient){
	$("#titreMail").text("Bonjour "+nomClient+",");
	$("#hiddenEmail").val(emailAdd);
	$("#modalApercuEmail").foundation('reveal', 'open');
}
function envoi(){
	$.post("includes/ink/mailFacture.php", { email: $("#hiddenEmail").val(), message: $("#contenumail").val(), sujet: $("#sujetMail").val() } );
	$("#modalApercuEmail").foundation('reveal', 'close');
	$("#contenumail").val("Votre texte ici.");
}
</script>


