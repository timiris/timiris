<?php
$tbAllTables = array();
require_once 'connection.php';
echo"<br>Début chargement All tables " . date("Y-m-d H:i:s").'<br>';
$reqChargeAllTables = 'SELECT * FROM pg_catalog.pg_tables where tablename like \'tmp_data_%\'';
try{
	$result = $connection->query($reqChargeAllTables);
	while ($table = $result->fetch(PDO::FETCH_OBJ)){
		if($table->tablename != 'data_attribut'){
			$req = 'INSERT INTO '.$table->tablename.' (numero) SELECT numero from data_attribut';
			$res = $connection->query($req);
			echo $table->tablename.' is finished <br>';
		}
	}
}
catch(PDOException $e){
	echo $e->getMessage();
}
echo"<br>Fin chargement All tables " . date("Y-m-d H:i:s");
?>