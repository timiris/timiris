<?php

function registerRelance($conn, $idbns, $dt) {
    if (count($idbns)) {
        $req = "update app_bonus set dt_sms = '$dt' where id in (" . implode(",", $idbns) . ")";
        $res = $conn->query($req);
    }
}

try {
    $rep = '../../';
    $result_send = array();
    $sendError = 0;
    require_once $rep . "conn/connection.php";
    $resVerif = $connection->query("SELECT * FROM sys_cron WHERE type = 'bonus' and etat = TRUE");
    if (!$resVerif->rowCount())
        exit();
    require_once $rep . "fn_sendSMS.php";
    $req = "SELECT is_lang_ar lang, att.numero, sms_bonus_ar, sms_bonus_fr, dt_droit, ab.id from app_bonus ab 
            join app_campagne ac on ab.fk_id_campagne =ac.id 
            and fk_id_groupe=0 and dt_sms is null and sms_bonus_ar is not null
            join app_bonus_details abd on ab.id = abd.id_bonus and abd.dt_bns is not null
            JOIN data_attribut att on att.numero = ab.numero
	    where sms_bonus_ar !='' or sms_bonus_fr !=''
            group by sms_bonus_fr , is_lang_ar,att.numero, sms_bonus_ar, dt_droit, ab.id having count(dt_bns) != count(*)
            UNION
SELECT is_lang_ar lang, att.numero, sms_bonus_ar, sms_bonus_fr, dt_droit, ab.id from app_bonus ab 
            join app_campagne_groupe ag on ab.fk_id_groupe = ag.id
            and dt_sms is null and sms_bonus_ar is not null
            join app_bonus_details abd on ab.id = abd.id_bonus and abd.dt_bns is not null
            JOIN data_attribut att on att.numero = ab.numero
	    where sms_bonus_ar !='' or sms_bonus_fr !=''
            group by sms_bonus_fr , is_lang_ar,att.numero, sms_bonus_ar, sms_bonus_fr, dt_droit, ab.id having count(dt_bns) != count(*)
order by dt_droit";
    $result = $connection->query($req);

    $nbrSMS = $nbrSMST = 0;
    $stTime = microtime_float();
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $numero = $ligne->numero;
        $id = $ligne->id;
        $sms_ar = $ligne->sms_bonus_ar;
        $sms_fr = $ligne->sms_bonus_fr;
        $is_lang_ar = $ligne->lang;
        if ($is_lang_ar == 'true') {
            $sms = $sms_ar;
            $enc = 2;
        } else {
            $sms = $sms_fr;
//            $enc = (mb_detect_encoding($sms) == 'UTF-8') ? 2 : 0;
            $enc = 0;
        }
        $dt = date('YmdHis');
        $ret = sendSMS($numero, $sms, 'MATTEL', $enc, 'bonus');
        $arr_sms = array('ar' => $sms_ar, 'fr' => $sms_fr);
        $inc = getIncrementation(1, $is_lang_ar, $arr_sms);
        $nbrSMS += $inc;
        $nbrSMST += $inc;
        switch ($ret) {
            case -1:
                echo 'CURL_NOT_INSTALLED';
                break;
            case 1 :
                $result_send[] = $id;
                break;
            default :
                echo "SMS send to $numero, has ERROR : $ret \n\r";
                $sendError++;
        }
        if ($nbrSMS >= LIMIT_SMS_SEC) {
            registerRelance($connection, $result_send, $dt);
            if ($nbrSMST > 100 && ($sendError / $nbrSMST) > 0.3) {
                echo "Taux d'échec trops élévé : $sendError sur $nbrSMST \n\r";
                exit();
            }
            $result_send = array();
            $nbrSMS = 0;
            $nw = microtime_float();
            $pause = $nw - $stTime;
            if ($pause < 1) {
                $pause = ceil((1 - $pause) * 1000000);
                usleep($pause);
                $dt = date('YmdHis');
            }
            $stTime = microtime_float();
            $dt = date('YmdHis');
        }
    }

    if (count($result_send))
        registerRelance($connection, $result_send, $dt);
} catch (Exception $e) {
    if (count($result_send))
        registerRelance($connection, $result_send, $dt);
    echo $e->getMessage();
}
?>