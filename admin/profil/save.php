<?php
if(!isset($_SESSION)) session_start();
require_once "../../fn_security.php";
check_session();
if (isset($_POST['profil']) && !empty($_POST['profil'])) {
    try {
        require_once '../../conn/connection.php';
        $connection->beginTransaction();
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        $profil = json_decode($_POST["profil"], true);
        foreach ($profil as $ch => $val) {
            if (trim(addslashes($val)) == '') {
                $retour['message'] = 'Information incompléte !!!!';
            }
        }
        if ($retour['message'] == '') {
            require_once '../../conn/connection.php';

            if ($profil['id'] != 'NewProfil') { // Modification
                $req = 'select * from sys_profil where id = ' . (int) $profil['id'];
                $res = $connection->query($req);
                if ($res->rowCount()) {
                    $req = "select nom from sys_profil where nom = '" . addslashes($profil['name_profil']) . "' and id != " . (int) $profil['id'];
                    $res = $connection->query($req);
                    if ($res->rowCount()) {
                        $retour['message'] = 'Profil existe déjà';
                        $retour['exec'] = 0;
                    } else {

                        $req = "update sys_profil set nom = '" . addslashes($profil['name_profil']) . "' where id = " . (int) $profil['id'];
                        $res = $connection->query($req);
                        // delete old permission
                        $req = 'delete from sys_actions_profil where id_profil = ' . (int) $profil['id'];
                        $res = $connection->query($req);
                        $retour['message'] = 'Mise à jour faite avec succès';
                        $retour['exec'] = 1;
                        $id = (int) $profil['id'];
                    }
                }
                else
                    $retour['message'] = 'Profil incorrecte';
            }else {  // Nouveau profil
                $req = "select nom from sys_profil where nom = '" . addslashes($profil['name_profil']) . "'";
                $res = $connection->query($req);
                if ($res->rowCount()) {
                    $retour['message'] = 'Profil existe déjà';
                } else {
                    $req = "insert into sys_profil (nom) VALUES ('" . addslashes($profil['name_profil']) . "')";
                    $res = $connection->query($req);
//                    $res->execute();
                    $id = $connection->lastInsertId('profil_id_seq');
                    $retour['exec'] = 1;
                    $retour['message'] = 'Profil créé avec succès';
                }
            }
            if ($retour['exec']) {
                unset($profil['id']);
                unset($profil['name_profil']);
                $arr = array();
                foreach ($profil as $key => $val) {
                    if ($val == 'on')
                        $arr[] = '(' . $id . ', ' . $key . ')';
                }
                $req = 'insert into sys_actions_profil (id_profil, id_action) values ' . implode(', ', $arr);
                $res = $connection->query($req);
                $connection->commit();
            }
        }
    } catch (PDOException $e) {
        $connection->rollback();
        $retour['exec'] = 0;
        $retour['message'] = $e->getMessage();
    }
    echo json_encode($retour);
}
?>
