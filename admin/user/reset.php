<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
if (isset($_POST['user']) && !empty($_POST['user'])) {
    try {
        $retour = array();
        $retour['message'] = '';
        $user = $_POST["user"];
//        $user = 85;
        require_once '../../conn/connection.php';
        $req = "update sys_users set pwd = 'timirisDefaultPassword' where id = $user";
        $res = $connection->query($req);
        if ($res->rowCount())
             $retour['message'] = 'Mot de passe initialisé avec succès';
        else
             $retour['message'] = "Mot de passe n'a pas été initialisé,\r\nveuillez contactez votre administrateur";
            echo json_encode($retour);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>