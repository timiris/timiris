<?php

require_once "../fn_security.php";
check_session();
require_once "../conn/connection.php";
require_once "../lib/tbLibelle.php";
require_once "fn/fn_decode_ciblage.php";
require_once "fn/fn_getDateRel.php";
$tbRetour['exec'] = 0;
$tbRetour['req'] = '';
$tbRetour['message'] = '';
if (!isset($_POST['idCible']) || empty($_POST['idCible']))
    exit();
$idCible = $_POST['idCible'];

$req = 'SELECT * FROM app_cibles WHERE id = ' . $idCible;
// ORDER BY type ASC, poids DESC
try {
    $result = $connection->query($req);
    if ($result->rowCount()) {
        //$tbRetour['req'] = $req;
        $ligne = $result->fetch(PDO::FETCH_OBJ);
        $inpt = '<p><input class="button12 black executer_cible" style = "width:80px ;font-size : 70%; font-weight:bold;"  type = "button" value = "Executer" name = "executer' . $idCible . '"></p>';
        $inpt .= '<form method = "POST" action = "ciblage/exporter_cible.php"  target="_blank">
			<input type = "hidden" name = "idCible" value = "' . $idCible . '">
			<input class="button12 black" style = "width:80px; font-size : 70%; font-weight:bold;" type = "submit" value = "Exporter" name = "exporter' . $idCible . '">
			</form>
			';
        $inpt .= '<p><input class="button12 black supprimer_cible" style = "width:80px; font-size : 70%; font-weight:bold;"  type = "button" value = "Supprimer"  name = "supprimer' . $idCible . '"></p>';
        $tbRetour['message'] = '<td colspan = "4">
		Association entre les groupes : ' . $tbAssocGr[strtoupper($ligne->association_group)] . '<ul>';
        $tbRetour['message'] .= decode_ciblage($ligne->cible, 1);
        $tbRetour['message'] .= '</ul></td><td>' . $inpt . '</td>';
    }
    $tbRetour['exec'] = 1;
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}
echo json_encode($tbRetour);
?>