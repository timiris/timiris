<?php

function registerTeasing($conn, $numeros, $dt, $idC) {
    if (count($numeros)) {
        foreach ($numeros as $lang => $nums) {
            $conn->query("update app_campagne_cible set dt_teasing = '$dt', is_lang_ar = '$lang'
                where fk_id_campagne = $idC and numero in ('" . implode("','", $nums) . "')");
            $conn->query('update app_campagne set nbr_teasing = nbr_teasing + ' . count($nums) . ' where id = ' . $idC);
        }
    }
}

try {
    $rep = '../';
    $nbrPauseMax = 5;
    $sendError = $nbrPause = 0;
    require_once $rep . "conn/connection.php";
    $resVerif = $connection->query("SELECT * FROM sys_cron WHERE type = 'teasi' and etat = TRUE");
    if (!$resVerif->rowCount())
        exit();
    require_once $rep . "fn_sendSMS.php";
    require_once $rep . "defs.php";
    $req = "SELECT id, broadcast, nom, sms_ar, sms_fr FROM app_campagne WHERE nbr_cible > nbr_teasing and etat = " . CMP_ENCOURS . ' and broadcast != 0 order by dt_lancement_reelle';
    $result = $connection->query($req);
    while ($nbrPause < $nbrPauseMax && $ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $idCmp = $ligne->id;
        $result_send = array();
        echo date('YmdHis') . " Start Sending Teasing for campaign " . $ligne->nom . " ($idCmp) \n\r";
//        $sms_ar_s = $ligne->sms_ar;
        // $sms_fr_s = $ligne->sms_fr;
        $broadcast = $ligne->broadcast;
        $sms_fr_s = ($broadcast == 3) ? '' : $ligne->sms_fr;
        $sms_ar_s = ($broadcast == 4) ? '' : $ligne->sms_ar;
//        $encFr = mb_detect_encoding($sms_fr_s);
//        $enc = ($encFr == 'UTF-8') ? 2 : 0;
        $req_list = 'select at.numero, at.is_lang_ar , nom, balance, nni, points_fidelite, dt_active, dt_active_stop, dt_suspend_stop 
            FROM app_campagne_cible cible
            JOIN data_attribut at on at.numero = cible.numero
            where fk_id_campagne = ' . $idCmp . ' and dt_teasing is null LIMIT 10000';
        $res_list = $connection->query($req_list);
        $stTime = microtime_float();
        $dt = date('YmdHis');
        $nbrSMS = $nbrSMST = $nbrParts = 0;
        while ($nbrPause < $nbrPauseMax && $ligne_list = $res_list->fetch(PDO::FETCH_OBJ)) {
            $numero = $ligne_list->numero;
            $msisdn = substr($ligne_list->numero, -8);
            $nom = $ligne_list->nom;
            $solde = (int) ($ligne_list->balance / 100);
            $sfidelity = $ligne_list->points_fidelite;
            $nni = $ligne_list->nni;
            $dt_act = substr($ligne_list->dt_active, 0, 8);
            $dt_susp = substr($ligne_list->dt_active_stop, 0, 8);
            $dt_des = substr($ligne_list->dt_suspend_stop, 0, 8);
            $is_lang_ar = $ligne_list->is_lang_ar;

            eval("\$sms_ar = \"$sms_ar_s\";");
            eval("\$sms_fr = \"$sms_fr_s\";");
            $sms_ar = trim($sms_ar);
            $sms_fr = trim($sms_fr);
            if ($sms_ar != '' || $sms_fr != '') {
                $ret = bulkSMS($numero, $is_lang_ar, $broadcast, $sms_ar, $sms_fr);
                $arr_sms = array('ar' => $sms_ar, 'fr' => $sms_fr);
                $inc = getIncrementation($broadcast, $is_lang_ar, $arr_sms);

//                $nbrSMS += $broadcast;
                $nbrSMS += $inc;
                $nbrParts += $inc;
                $nbrSMST++;
                switch ($ret) {
                    case -1:
                        echo "CURL_NOT_INSTALLED \n\r";
                        break;
                    case 1 :
                        $result_send[$is_lang_ar][] = $numero;
                        break;
                    default :
                        echo "SMS send to $numero, has ERROR : $ret \n\r";
                        $sendError++;
                }
            }
            if ($nbrSMS >= LIMIT_SMS_SEC) {
                $nbrPause = 0;
                registerTeasing($connection, $result_send, $dt, $idCmp);
                if ($nbrSMST > 200 && ($sendError / $nbrSMST) > 0.3) {
                    echo "Taux d'échec trops élévé : $sendError sur $nbrSMST \n\r";
                    $connection->query("update sys_cron set etat = false where type = 'teasi'");
                    exit();
                }
                $result_send = array();
                $nbrSMS = 0;
                $nw = microtime_float();
                $pause = $nw - $stTime;
                if ($pause < 1) {
                    $pause = ceil((1 - $pause) * 1000000);
                    usleep($pause);
                    while ($nbrPause < $nbrPauseMax && getStoreSize() > 2000) {
                        echo date('YmdHis') . ' Pause store size : ' . getStoreSize() . "\n\r";
                        usleep(1000000);
                        $nbrPause++;
                    }
                    $dt = date('YmdHis');
                }
                $stTime = microtime_float();
                $dt = date('YmdHis');
            }
        }
        if (count($result_send)) {
            registerTeasing($connection, $result_send, $dt, $idCmp);
            $result_send = array();
        }
        echo date('YmdHis') . " End Sending Teasing for campaign " . $ligne->nom . " ($idCmp), $nbrSMST TEASING ($nbrParts parts) which $sendError errors \n\r";
    }
} catch (Exception $e) {
    if (count($result_send)) {
        registerTeasing($connection, $result_send, $dt, $idCmp);
    }
    echo $e->getMessage() . "\n\r";
}
?>