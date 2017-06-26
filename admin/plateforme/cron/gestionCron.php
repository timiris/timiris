<?php

if(!isset($rep))
    $rep = '../../../';
if (!isset($_SESSION))
    session_start();
require_once $rep."fn_security.php";
check_session();
if (isset($_POST['type']) && !empty($_POST['type'])) {
    try {
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        $type = $_POST["type"];
        require_once $rep.'conn/connection.php';
        $req = "select etat from sys_cron where type = '$type'";
        $res = $connection->query($req);
        if ($res->rowCount()) {
            $ligne = $res->fetch(PDO::FETCH_OBJ);
            if ($ligne->etat) {
                $nvEtat = 'false';
                $typeStp = ($type == 'b') ? 'msc' : $type;
                $cmd ="ps -ef | grep import_$typeStp | grep -v grep | awk '{print $2}' | xargs kill -9";
                exec($cmd);
            } else {
                $nvEtat = 'true';
            }
            $req_upd = "update sys_cron set etat = $nvEtat where type = '$type'";
            $res = $connection->query($req_upd);
            $retour['exec'] = 1;
            echo json_encode($retour);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>