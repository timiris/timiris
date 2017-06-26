<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
if (isset($_POST['profil']) && !empty($_POST['profil'])) {
    try {
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        $profil = $_POST["profil"];
        require_once '../../conn/connection.php';
        $req = "select etat from sys_profil where id = $profil";
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
            $req_upd = "update sys_profil set etat = $nvEtat where id = $profil";
            $res = $connection->query($req_upd);
            $retour['exec'] = 1;
            echo json_encode($retour);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>