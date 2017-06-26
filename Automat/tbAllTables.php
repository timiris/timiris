<?php
$tbAllTables = array();
require_once 'connection.php';
// $reqChargeAllTables = 'SHOW TABLES LIKE "data_%"';
$reqChargeAllTables = 'SELECT * FROM pg_catalog.pg_tables where tablename like \'data_%\'';
try{
	$result = $connection->query($reqChargeAllTables);
	while ($table = $result->fetch(PDO::FETCH_OBJ)){
		$tbAllTables[] = $table->tablename;
	}
}
catch(PDOException $e){
	echo $e->getMessage();
}
// print_r($tbAllTables);
// exit();
?>