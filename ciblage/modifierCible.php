<?php

if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
require_once "../defs.php";
require_once "../conn/connection.php";
require_once "fn/drawCible.php";
require_once "fn/fn_getDateRel.php";
require_once "../lib/tbLibelle.php";
$tbRetour['exec'] = 0;
$tbRetour['message'] = '';
if (isset($_POST['idCible'])) {
    $idCC = (int) $_POST['idCible'];
    if ($idCC > 0) {
        $res = $connection->query("SELECT count(*) nbr FROM app_campagne WHERE id_cible = $idCC")->fetch(PDO::FETCH_OBJ);
        $nbr = $res->nbr;
        if (!$nbr) {
            $cib = $connection->query('SELECT * FROM app_cibles WHERE id = ' . $idCC);
            $liCib = $cib->fetch(PDO::FETCH_OBJ);
            $assoc_group = $liCib->association_group;
            $CC = $liCib->cible;
        } else {
            $tbRetour['message'] = 'Cible déja utilisée par une campagne';
            echo json_encode($tbRetour);
            exit();
        }
    }
    ob_start();
    drawCible($idCC, $CC, $assoc_group, $connection);
    $tbRetour['message'] =  ob_get_clean();
    $tbRetour['exec'] = 1;
} elseif (isset($_POST['parms'])) {
    $tbRetour['message'] = 'Modification faite avec succès.';
    try {
        require_once "fn/fn_generateArrayParams.php";
        require_once "fn/fn_generateRequete.php";
        require_once $rep . "nameTables.php";
        $tab_params = json_decode($_POST["parms"], true);
        if (isset($tab_params["cibleId"])) {
            $cibleId = $tab_params["cibleId"];
            unset($tab_params["cibleId"]);
        }
        $associationGroupe = $tab_params["associationGroupe"];
        unset($tab_params["associationGroupe"]);
//        print_r($tab_params);
        $tables = generateArrayParams($tab_params);
        $connection->query('UPDATE app_cibles SET association_group = \'' . $associationGroupe . '\', cible =\'' . json_encode($tables) . '\' WHERE id = ' . $cibleId);
        $tbRetour['exec'] = 1;
    } catch (Exception $e) {
        $tbRetour['message'] = $e->getMessage();
    }
}
echo json_encode($tbRetour);
?>