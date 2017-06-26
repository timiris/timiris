<?php
if(!isset($_SESSION)) session_start();
require_once "../fn_security.php";
check_session(); 
require_once "../conn/connection.php";
$tbRetour['exec'] = 0;
$tbRetour['req'] = '';
$tbRetour['message'] = '';
if(!isset($_POST['idCmp']) || empty($_POST['idCmp']))
	exit();
$idCmp = (int) $_POST['idCmp'];

// $req = 'DELETE FROM app_cibles WHERE id = '.$idCible ;
$req = 'UPDATE app_campagne SET etat = 0 WHERE id = '.$idCmp ;
	// ORDER BY type ASC, poids DESC
try{
	$result = $connection->query($req);
	$tbRetour['exec'] = 1;
	$tbRetour['req'] = $req;
	$tbRetour['message'] = "Campagne supprimée avec succès";
}
catch(PDOException $e){
	$tbRetour['message'] = $e->getMessage();
}
echo json_encode($tbRetour);
?>