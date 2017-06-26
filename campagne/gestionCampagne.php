<?php

if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
$tbRetour['exec'] = 0;
$tbRetour['req'] = '';
$tbRetour['message'] = '';
try {
    if (!isset($_POST['idCmp']) || empty($_POST['idCmp']) || !isset($_POST['action']) || empty($_POST['action']) || !isset($_POST['motif']) || empty($_POST['motif'])
    )
        exit();
    require_once "../conn/connection.php";
    require_once "../defs.php";
    require_once "../Automat/mail/envoyer_mail.php";
//    print_r($_POST);
//    exit();
    $motif = $_POST['motif'];
    $idCmp = (int) $_POST['idCmp'];
    $result = $connection->query('SELECT ac.nbr_cible, ac.nbr_gc, ac.nom nom_cmp, ac.etat, chez_profil, profil_saisie ps, ru.id ru_id, ru.nom ru_nom, ru.prenom ru_prenom, ru.mail ru_mail
        FROM app_campagne ac 
        JOIN sys_users ru on createur = ru.id
        WHERE ac.id = ' . $idCmp);
    if ($result->rowCount()) {
        $li = $result->fetch(PDO::FETCH_OBJ);
        $nomCmp = $li->nom_cmp;
//        $chez = $li->chez_profil;
//        $ps = $li->ps;
        $nbrCible = $li->nbr_cible;
        $nbrGc = $li->nbr_gc;
        $createurNom = $li->ru_prenom . ' ' . $li->ru_nom;
        $createurMail = $li->ru_mail;
        $createurId = $li->ru_id;
        if ($createurId != $_SESSION['user']['id']) {
            $resU = $connection->query('SELECT * FROM sys_users WHERE id =' . $_SESSION['user']['id']);
            $liU = $resU->fetch(PDO::FETCH_OBJ);
            $acteurName = $liU->prenom . ' ' . $liU->nom;
            $acteurMail = $liU->mail;
        } else {
            $acteurName = $createurNom;
            $acteurMail = $createurMail;
        }
    }
    else
        exit();
    $cnd = '';
    switch ($_POST['action']) {
        case 'arreter':
            $newStatus = CMP_ARRETEE;
            $tbRetour['message'] = "Campagne arrêter avec succès";
            $body = "Bonjour $createurNom,<br>La campagne : ' ($nomCmp) , que vous avez créée est arrêter par : $acteurName.<br>
                    Le motif d'arrêt est : $motif.<br>
                    <h4>TIMIRIS Plateforme</h4>";
            $subject = 'Arrêt campagne';

            $rqroi = $connection->query('select sum(valorisation) v_bonus from app_bonus_details abd JOIN app_bonus ab on ab.id = abd.id_bonus and ab.fk_id_campagne = ' . $idCmp);
            if ($rqroi->rowCount())
                $v_bonus = $rqroi->fetch(PDO::FETCH_OBJ)->v_bonus;
            else
                $v_bonus = 0;

            $rqroi = $connection->query('select cible, gc FROM app_campagne_kpi WHERE fk_id_campagne =' . $idCmp . " and tbname ='data_consommation_total'");
            $rqroi = $rqroi->fetch(PDO::FETCH_OBJ);
            $consCible = ($rqroi->cible / $nbrCible - (($nbrGc) ? $rqroi->gc / $nbrGc : 0)) * $nbrCible / 100;

            $roi = $consCible - $v_bonus;

            $cnd = " , dt_fin_relle = '" . date('Y-m-d H:i:s') . "', roi = $roi ";
            break;
        case 'pause':
            $newStatus = CMP_SUSPENDUE;
            $tbRetour['message'] = "Campagne mise en pause avec succès";
            $body = "Bonjour $createurNom,<br>La campagne : ' ($nomCmp) , que vous avez créée est mise en pause par : $acteurName.<br>
                    Le motif de mise en pause est : $motif.<br>
                    <h4>TIMIRIS Plateforme</h4>";
            $subject = 'Mise en pause de la campagne';
            break;
        case 'rejeter':
            $newStatus = CMP_REJETEE;
            $tbRetour['message'] = "Campagne rejeter avec succès";
            $body = "Bonjour $createurNom,<br>La campagne : ' ($nomCmp) , que vous avez créée est rejetée par : $acteurName.<br>
                    Le motif de rejet est : $motif.<br>
                    <h4>TIMIRIS Plateforme</h4>";
            $subject = 'Rejet campagne';
            break;
        case 'activer':
            $newStatus = CMP_ENCOURS;
            $tbRetour['message'] = "Campagne activer avec succès";
            $body = "Bonjour $createurNom,<br>La campagne : ' ($nomCmp) , que vous avez créée est réactivée par : $acteurName.<br>
                    Le motif de réactivation est : $motif.<br>
                    <h4>TIMIRIS Plateforme</h4>";
            $subject = 'Réactivation campagne';
            break;
        case 'valider':
            $newStatus = CMP_SOUMISE;
            $tbRetour['message'] = "Campagne valider avec succès";
            $subject = 'Validation campagne';
            break;
        default:
            exit();
    }
    $connection->beginTransaction();
    $connection->query("update app_campagne set etat = " . $newStatus . " $cnd  WHERE id = " . $idCmp);
    $req = "insert into app_campagne_wf (fk_id_campagne, dt_action, id_profil, id_user, new_status, commentaire) 
                VALUES ($idCmp, '" . date('YmdHis') . "', " . $_SESSION['user']['profil'] . ", " . $_SESSION['user']['id'] . ", $newStatus, '" . str_replace("'", "''", $motif) . "')";
    $result = $connection->query($req);
    if ($connection->commit())
        $tbRetour['exec'] = 1;
    else
        $connection->rollBack();

    if ($createurId != $_SESSION['user']['id'] && $createurMail != '') {    // envoyer un mail
        $arr_cc = $arr_address = array();
        $arr_address[$createurMail] = $createurNom;
        if ($acteurMail != '')
            $arr_cc[$acteurMail] = $acteurName;
//        $tbRetMail = sendMail($subject, $body, $arr_address, $arr_cc);
//        if (!$tbRetMail['send'])
//            $tbRetour['message'] .= $tbRetMail['message'];
    }
} catch (Exception $e) {
    $connection->rollBack();
    $tbRetour['message'] = $e->getMessage();
    print_r($e);
}
echo json_encode($tbRetour);
?>