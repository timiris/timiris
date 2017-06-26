<?php
require_once "../fn_security.php";
check_session();
require_once "../conn/connection.php";
$tbRetour['exec'] = 0;
$tbRetour['req'] = '';
$tbRetour['message'] = '';
if(!isset($_POST['idCible']) || empty($_POST['idCible']))
	exit();
$idCible = $_POST['idCible'];

// $req = 'DELETE FROM app_cibles WHERE id = '.$idCible ;
$req = 'UPDATE app_cibles SET etat = 0 WHERE id = '.$idCible ;
	// ORDER BY type ASC, poids DESC
try{
	$result = $connection->query($req);
	$tbRetour['exec'] = 1;
	$tbRetour['req'] = $req;
	$tbRetour['message'] = "Cible supprimée avec succes";
}
catch(PDOException $e){
	$tbRetour['message'] = $e->getMessage();
}
echo json_encode($tbRetour);
?>