<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
if (isset($_POST['user']) && !empty($_POST['user'])) {
    try {
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        $user = json_decode($_POST["user"], true);
        foreach ($user as $ch => $val) {
            if (trim(addslashes($val)) == '') {
                $retour['message'] = 'Information incompléte !!!!';
            }
        }
        if ($retour['message']=='') {
            require_once '../../conn/connection.php';
            if (isset($user['iduser'])) { // Modification
                $req = 'select * from sys_users where id = ' . (int) $user['iduser'];
                $res = $connection->query($req);
                if ($res->rowCount()) {
                    $req = "update sys_users set nom = '" . addslashes($user['nom']) . "', prenom = '" . addslashes($user['prenom']) . "',
                        mail = '" . addslashes($user['mail']) . "', phone = '" . addslashes($user['phone']) . "',
                        fk_id_profil = " . $user['profil'] . " where id = " . $user['iduser'];
                    $res = $connection->query($req);
                    $retour['message'] = 'Modification faite avec succès';
                    $retour['exec'] = 1;
                }
                else
                    $retour['message'] = 'Utilisateur incorrecte';
            }else {  // Nouvel utilsateur
                $req = "select id, login from sys_users where login = '" . addslashes($user['login']) . "'";
                $res = $connection->query($req);
                if ($res->rowCount()) {
                    $retour['message'] = 'Login déja utilisé ';
                } else {
                    $req = "insert into sys_users (nom, prenom, login, phone, mail, fk_id_profil) VALUES 
                    ('" . addslashes($user['nom']) . "', '" . addslashes($user['prenom']) . "', '" . addslashes($user['login']) . "', 
                     '" . addslashes($user['phone']) . "', '" . addslashes($user['mail']) . "', " . $user['profil'] . ")";
                    $res = $connection->query($req);
                    $retour['exec'] = 1;
                    $retour['message'] = 'Utilisateur créé avec succès';
                }
            }
        }
    } catch (Exception $e) {
        $retour['message'] = $e->getMessage();
        $retour['exec'] = 0;
    }
    echo json_encode($retour);
}
?>
