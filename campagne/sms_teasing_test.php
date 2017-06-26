<?php

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

try {
    $rep = '../';
    $result_send = array();
    $sendError = 0;
    require_once $rep . "fn_sendSMS.php";
    require_once $rep . "defs.php";
//    $sms_fr = "Nous voulons tester la limitation des SMS par le module TEASING et la concaténation des messages qui dépassent la taille limite.
//Ce message doit être spliter en deux SMS, car contient plus de 100 caractére et sera codé en 7 bits";
//    $sms_ar = "نريد لاختبار حدود وحدة الإثارة SMS وسلسلة من الرسائل التي تتجاوز الحد الأقصى للحجم.
//يجب تقسيم هذه الرسالة الى قسمين SMS، كما أنه يحتوي سيتم ترميز أكثر من 100 حرف وبت 7";
//    echo iconv('UTF-8', 'LATIN1', $sms_fr);exit();
//    $arr = array();
//    for ($i = 0; $i < 50; $i++) {
//        $arr[] = array('num' => '22238773477', 'sms_ar' => $sms_ar, 'sms_fr' => $sms_fr);
//        $arr[] = array('num' => '22233222255', 'sms_ar' => $sms_ar, 'sms_fr' => $sms_fr);
//    }
    $arr = file("/tim_DATA/cdrs/chargement/msc/FID_6_0606.txt");
    echo count($arr);
    $arr = array('22238773477','22233222255');
//    exit();
    $sms = 'Nouveau! Programme fidélité KADO de Mattel : cumulez des points à chaque consommation et convertissez les en minutes, SMS ou connexion gratuites. Inscription gratuite au *190#';
    $stTime = microtime_float();
    $dt = date('YmdHis');
    $nbrSMS = $nbrSMST = $nbrParts = 0;
    echo $stTime;
    $broadcast = 1;
    $is_lang_ar = 2;
    foreach ($arr as $numero) {
        $numero = substr($numero, 11);
        if ($sms != '') {
            $ret = sendSMS($numero, $sms, 'PromoMattel', 0);
            $lnfr = iconv_strlen($sms, 'LATIN1');
            $inc = 0;
            if ($broadcast == 1) {
                if ($is_lang_ar == 1) {    //SMS AR
                    $inc = (int) ($lnar / 70);
                    $inc += (($lnar % 70) != 0) ? 1 : 0;
                } else {    //SMS FR
                    $inc = (int) ($lnfr / 160);
                    $inc += (($lnfr % 160) != 0) ? 1 : 0;
                }
            } else {
                $inc = (int) ($lnar / 70);
                $inc += (($lnar % 70) != 0) ? 1 : 0;
                $inc += (int) ($lnfr / 160);
                $inc += (($lnfr % 160) != 0) ? 1 : 0;
            }
//                $nbrSMS += $broadcast;
            $nbrSMS += $inc;
            $nbrParts += $inc;
            $nbrSMST++;
            switch ($ret) {
                case -1:
                    echo "CURL_NOT_INSTALLED \n\r";
                    break;
                case 1 :
                    $result_send[] = $numero;
                    break;
                default :
                    echo "SMS send to $numero, has ERROR : $ret \n\r";
                    $sendError++;
            }
        }
        if ($nbrSMS >= $limtSec) {
            //registerTeasing($connection, $result_send, $dt, $idCmp);
            if ($nbrSMST > 200 && ($sendError / $nbrSMST) > 0.3) {
                echo "Taux d'échec trops élévé : $sendError sur $nbrSMST \n\r";
            }
            $result_send = array();
            $nbrSMS = 0;
            $nw = microtime_float();
            $pause = $nw - $stTime;
            if ($pause < 1) {
                $pause = ceil((1 - $pause) * 1000000);
                echo "pause de $pause, nbr parts est $nbrParts \n\r";
                usleep($pause);
                $dt = date('YmdHis');
            }
            $stTime = microtime_float();
            echo $stTime;
            $dt = date('YmdHis');
        }
    }
} catch (Exception $e) {
    echo $e->getMessage() . "\n\r";
}
?>