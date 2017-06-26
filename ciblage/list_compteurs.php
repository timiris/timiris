<?php
require_once "../fn_security.php";
check_session(); 
$tabUnite = $options = "";
require_once "../conn/connection.php";
if(isset($_POST["id_nature"]) && !empty($_POST["id_nature"])){
	$id_nature = $_POST["id_nature"];
	$req = "SELECT * FROM ref_compteurs WHERE etat = '1' and fk_id_type='".$id_nature."' ORDER BY id";
		// ORDER BY type ASC, poids DESC
	try{
		$result = $connection->query($req);
		while($ligne = $result->fetch(PDO::FETCH_OBJ)){
			if($tabUnite == ""){
				$tabUnite = json_decode($ligne->unite, true);
				// var_dump($tabUnite);
			}
			$options .= "<option value = ".strtolower($ligne->code_cmpt).">".ucfirst(strtolower($ligne->libelle))."</option>";
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	echo $options;
}
?>