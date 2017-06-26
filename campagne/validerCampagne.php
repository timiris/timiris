<?php

try {
    if (!isset($_SESSION))
        session_start();
    require_once "../fn_security.php";
    check_session();
    require_once "../defs.php";
    require_once "../conn/connection.php";
    require_once "../Automat/mail/envoyer_mail.php";
    $tbRetour['exec'] = 0;
    $tbRetour['req'] = '';
    $tbRetour['message'] = '';
    if (!isset($_POST['idCmp']) || empty($_POST['idCmp']))
        exit();
    $connection->query('BEGIN');
    $idCmp = (int) $_POST['idCmp'];
    $motif = $_POST['motif'];
    $req = 'SELECT ac.nom, ac.etat, chez_profil, profil_saisie ps, ru.nom ru_nom, ru.prenom ru_prenom, ru.mail ru_mail
        FROM app_campagne ac 
        join sys_users ru on createur = ru.id WHERE ac.id = ' . $idCmp;
    $result = $connection->query($req)->fetch(PDO::FETCH_OBJ);
    $etat = $result->etat;
    $nom_cmp = $result->nom;
    $chez = $result->chez_profil;
    $ps = $result->ps;
    $ru_nom = $result->ru_prenom . ' ' . $result->ru_nom;
    $ru_mail = $result->ru_mail;
    $req = 'select wf from ref_wf where profil = ' . $ps;
    $result = $connection->query($req);
    if ($result->rowCount()) {
        $ligne = $result->fetch(PDO::FETCH_OBJ);
        $wf = json_decode($ligne->wf, true);
        $arr_cc = $arr_address = array();
        if (isset($wf[$chez]) || in_array($chez, $wf)) {
            if (isset($wf[$chez])) {
                $newStatus = CMP_SOUMISE;
                $req = 'UPDATE app_campagne SET etat = '.CMP_SOUMISE.', chez_profil = ' . $wf[$chez] . ' WHERE id = ' . $idCmp;

                //******* Préparation Mail
                $req_dest = 'select * from sys_users where fk_id_profil = ' . $wf[$chez];
                $res_dest = $connection->query($req_dest);
                while ($li_dest = $res_dest->fetch(PDO::FETCH_OBJ)) {
                    if ($li_dest->mail)
                        $arr_address[$li_dest->mail] = $li_dest->prenom . ' ' . $li_dest->nom;
                }
                if ($ru_mail)
                    $arr_cc[$ru_mail] = $ru_nom;
                $body = 'Bonjour,<br>La campagne : ' . $nom_cmp . ' , créée par : ' . $ru_nom . ', est en attente de votre validation.<br><br>
                    <h4>TIMIRIS Plateforme</h4>';
            }
            else {
                $newStatus = CMP_ATTENTE;
                $req = 'UPDATE app_campagne SET etat = '.CMP_ATTENTE.', chez_profil = 0 WHERE id = ' . $idCmp;
                if ($ru_mail)
                    $arr_address[$ru_mail] = $ru_nom;
                $body = 'Bonjour,<br>La campagne : ' . $nom_cmp . ' , que vous avez créez vient d\'être définitivement validée.<br><br>
                    <h4>TIMIRIS Plateforme</h4>';
            }
            $result = $connection->query($req);
            $req = "insert into app_campagne_wf (fk_id_campagne, dt_action, id_profil, id_user, new_status, commentaire) 
                VALUES ($idCmp, '" . date('YmdHis') . "', " . $_SESSION['user']['profil'] . ", " . $_SESSION['user']['id'] . ", $newStatus, '".str_replace("'", "''", $motif)."')";
            $result = $connection->query($req);
            if ($connection->query('COMMIT')) {
                $tbRetour['exec'] = 1;
                $tbRetour['message'] = 'Campagne validée avec succès ';
                $subject = 'Validation campagne : ' . $nom_cmp;
                $tbRetMail = sendMail($subject, $body, $arr_address, $arr_cc);
                if (!$tbRetMail['send'])
                    $tbRetour['message'] .= $tbRetMail['message'];
            }
            else
                $connection->query('ROLLBACK');
        } else {
            $tbRetour['message'] = 'Vous n\'avez pas un workFlow de validation !!!!';
        }
    } else {
        $tbRetour['message'] = 'Vous n\'avez pas un workFlow de validation !!!!';
    }
} catch (PDOException $e) {
    $connection->query('ROLLBACK');
    $tbRetour['message'] = $e->getMessage();
}
echo json_encode($tbRetour);
?>