<?php
$idGroup = $idCritere = 1;
if(isset($_POST["idGroup"]) and !empty($_POST["idGroup"]) and isset($_POST["idCritere"]) and !empty($_POST["idCritere"])){
	$idGroup = $_POST["idGroup"];
	$idCritere = $_POST["idCritere"];
}

$idDOM = $idGroup.'_'.$idCritere;
$table = $_POST["nat_tr"].'_'.$_POST["tp_dn"];
// if($_POST["nat_tr"] == 1)
	// $table = 1;
$titreCritere = "Critére : {Nature Trafic : <b>".$_POST["nat_tr_txt"]."</b>}/{Type données : <b>".$_POST["tp_dn_txt"]."</b>}";
// $titreCritere = "Critére : {<b>".$_POST["tp_dn_txt"]." ".$_POST["nat_tr_txt"]."</b>}";
// $titreCritere = "Critére : {Nature Trafic : <b>".$_POST["nat_tr_txt"]."</b>}/{Type données : <b>".$_POST["tp_dn_txt"]."</b>}";
?>

<div id="critere_<?php echo $idDOM; ?>" class = "divCritere" style = "border :1px solid blue; margin-top:5px;">
	<input type="hidden" id="natureType_<?php echo $idDOM; ?>" class="critere" value = "<?php echo $table; ?>"/>
	<table width = "95%">
	<tbody>
		<tr>
			<td colspan="2" align = "center"><?php echo $titreCritere; ?></td>
			<th align="right">
			 <div class="SupprimerDIV" name = "critere_<?php echo $idDOM; ?>" title="Supprimer le critére"></div>
			</th>
		</tr>
		<tr>
			<td><label for="idTypeCompteur_<?php echo $idDOM; ?>">Type compteur : </label></td>
			<td>
				<select id="idTypeCompteur_<?php echo $idDOM; ?>" class="critere">
					<?php
						require_once "../conn/connection.php";
						$options = "";
						$req = "SELECT * FROM tables_compteurs WHERE etat = '1' and fk_id_nature='".$_POST["nat_tr"]."' ";
							// ORDER BY type ASC, poids DESC
						try{
							$result = $connection->query($req);
							if ($result->rowCount()){
								while($ligne = $result->fetch(PDO::FETCH_OBJ)){
									$options .= "<option value = ".strtolower($ligne->libelle).">".ucfirst(strtolower($ligne->libelle))."</option>";
								}
							}
						}
						catch(PDOException $e)
						{
							echo $e->getMessage();
						}
						echo $options;
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="idUnitePeriodique_<?php echo $idDOM; ?>">Unité périodique : </label></td>
			<td>
				<select id="idUnitePeriodique_<?php echo $idDOM; ?>" class="unite_periodique critere">
					<option value="j">Jour</option>
					<option value="s">Semaine</option>
					<option value="m">Mois</option>
					<option value="a">Année</option>
				</select>
				<span id="idLibellePeriode_<?php echo $idDOM; ?>" style = "margin-left:15px;">historique : 31 jours</span>
			</td>
		</tr>
		<tr>
			<td><label for="tgcPeriodSelector_<?php echo $idDOM; ?>">Période : </label></td>
			<td>
				<select id="idPeriodeFrom_<?php echo $idDOM; ?>" class = "critere">
					<?php
						require_once "../conn/connection.php";
						$options = "";
						$req = "SELECT * FROM historique_correspondance WHERE type = 'j' ORDER BY h_date LIMIT 31";
							// ORDER BY type ASC, poids DESC
						try{
							$result = $connection->query($req);
							if ($result->rowCount()){
								while($ligne = $result->fetch(PDO::FETCH_OBJ)){
									$options .= "<option value = '".$ligne->h_date."'>".ucfirst(strtolower($ligne->h_date))."</option>";
									// $options .= "<option value = ".$ligne->champ.">".ucfirst(strtolower($ligne->h_date))."</option>";
								}
							}
						}
						catch(PDOException $e)
						{
							echo $e->getMessage();
						}
						echo $options;
						
						$uniteValeur = "";
						// $req = "SELECT unite FROM type_donnee WHERE id = ".$_POST["tp_dn"];
						$req = "SELECT unite_choix FROM type_donnee WHERE id = ".$_POST["tp_dn"];
						try{
							$result = $connection->query($req);
							if ($result->rowCount()){
								$ligne = $result->fetch(PDO::FETCH_OBJ);
								// $uniteValeur = ucfirst(strtolower($ligne->unite));
								$tabUnite = json_decode($ligne->unite_choix, true);
								$selUnite = '';
								if(isset($tabUnite) && count($tabUnite)){
									foreach($tabUnite as $u=>$l){
										$selUnite .= '<option value ='.$u.'>'.$l.'</option>';
									}
								}
								$uniteValeur = ucfirst(strtolower($ligne->unite_choix));
							}
						}
						catch(PDOException $e)
						{
							echo $e->getMessage();
						}
						
						if($_POST["nat_tr"] == 8 && $_POST["tp_dn"] == "")
							$uniteValeur = "activation";
					?>
				</select>
				<span style = "margin-left:20px; margin-right:20px;"> au </span>
				<select id="idPeriodeTo_<?php echo $idDOM; ?>" class = "critere">
				<?php
					echo $options;
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="idFormule_<?php echo $idDOM; ?>">Formule : </label></td>
			<td>
				<select id="idFormule_<?php echo $idDOM; ?>" class = "critere">
					<option value="least">Minimum</option>
					<option value="greatest">Maximum</option>
					<option value="SUM">Somme</option>
					<option value="AVG">Moyenne</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<select id="operateur_<?php echo $idDOM; ?>" class="critere">
					<option value=" = ">Egal</option>
					<option value=" != ">Différent de</option>
					<option value=" >= ">Sup ou Egal</option>
					<option value=" > ">Supérieur à </option>
					<option value=" <= ">Inf ou Egal</option>
					<option value=" < ">Inférieur de </option>
				</select>
			</td>
			<td>
				<input type="text" id="valeurCritere_<?php echo $idDOM; ?>" size = "8" class = "critere chiffre">
				<?php  echo '<SELECT style = "margin-left:20px;" id="untieValeur_'.$idDOM.'" class="critere">'.$selUnite.'</SELECT>'; ?>
			</td>
		</tr>
	</tbody>
	</table>
</div>