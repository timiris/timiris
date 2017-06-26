<?php
if(!isset($rep))
    $rep = '../../../';
if (!isset($_SESSION))
    session_start();
require_once $rep."fn_security.php";
check_session();
if (isset($_POST['idCmpt']) && !empty($_POST['idCmpt'])) {
    $infos = explode('|', $_POST['idCmpt']);
//    $idType = $infos[0];
//    $idCmpt = $infos[1];
    $idCmpt = $_POST['idCmpt'];
    try {
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        require_once $rep.'conn/connection.php';
        if (isset($_POST['name']) && !empty($_POST['name'])) {
            $name = $_POST['name'];
            $NewVal = $_POST['valeur'];
            $connection->query("UPDATE ref_compteurs SET libelle = '" . addslashes($name) . "', valorisation= $NewVal WHERE code_cmpt = '" . $idCmpt . "'");
            $retour['exec'] = 1;
        }
        if (isset($_POST['stat'])) {
            if ($_POST['stat'])
                $cnd = ($_POST['action'] == 'Activer') ? ' bonus = true ' : ' bonus = false ';
            else
                $cnd = ($_POST['action'] == 'Activer') ? ' etat = 1 ': ' etat = 0 ';
            $connection->query("UPDATE ref_compteurs SET $cnd WHERE code_cmpt = '" . $idCmpt . "'");
            $retour['exec'] = 1;
        }
    } catch (PDOException $e) {
        $retour['message'] = $e->getMessage();
    }

    echo json_encode($retour);
}
?>