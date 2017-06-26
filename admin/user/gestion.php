<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
if (isset($_POST['user']) && !empty($_POST['user'])) {
    try {
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        $user = $_POST["user"];
        require_once '../../conn/connection.php';
        $req = "select etat from sys_users where id = $user";
        $res = $connection->query($req);
        if ($res->rowCount()) {
            $ligne = $res->fetch(PDO::FETCH_OBJ);
            if ($ligne->etat == 1) {
                $nvEtat = 0;
                $retour['src'] = 'on';
                $retour['title'] = 'Activer';
            } else {
                $nvEtat = 1;
                $retour['src'] = 'off';
                $retour['title'] = 'Désactiver';
            }
            $req_ins = "update sys_users set etat = $nvEtat where id = $user";
            $res = $connection->query($req_ins);
            $retour['exec'] = 1;
            echo json_encode($retour);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>