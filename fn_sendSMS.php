<?php

$SMSaccount = array();
$SMSaccount['halys'] = array('unsername'=>'user_halys', 'password' => 'halysPWD');
$SMSaccount['bonus'] = array('unsername'=>'user_halys', 'password' => 'halysPWD');
$SMSaccount['huawei'] = array('unsername'=>'user_huawei', 'password' => 'huaweiPWD');

$SMSreponse = array(0 => true, 3 => true);

define('LIMIT_SMS_SEC', 90);
$limtSec = 90;

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

function getIncrementation($broadcast, $is_lang_ar, $sms) {
    $incAr = $incFr = 0;
    $lnar = iconv_strlen($sms['ar'], 'UTF-8');
    $lnfr = iconv_strlen($sms['fr'], 'LATIN1');
    if ($broadcast == 1) {
        if ($is_lang_ar == 'true') {    //SMS AR
            $incAr += (int) ($lnar / 70);
            $incAr += (($lnar % 70) != 0) ? 1 : 0;
        } else {    //SMS FR
            $incFr += (int) ($lnfr / 160);
            $incFr += (($lnfr % 160) != 0) ? 1 : 0;
        }
    } else {
        $incAr += (int) ($lnar / 70);
        $incAr += (($lnar % 70) != 0) ? 1 : 0;
        $incFr += (int) ($lnfr / 160);
        $incFr += (($lnfr % 160) != 0) ? 1 : 0;
    }
    return ($incAr >= $incFr) ? $incAr : $incFr;
}

function getStoreSize() {
    try {
        $url = "http://localhost:13000/status.xml";
        $ch = curl_init();
        if (!$ch)
            die('curl_init n\'a pas marché');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $size = (int) simplexml_load_string($data)->sms->storesize;
        return $size;
    } catch (Exception $e) {
        return 100;
    }
}

function bulkSMS($numero, $is_lang_ar, $broadcast, $sms_ar, $sms_fr) {
    //$frm = 'MATTEL';
    $frm = 'PromoMattel';
    if ($broadcast == 1) {
        if ($is_lang_ar == 'true') {
            $enc = 2;
            $sms = $sms_ar;
            $smsc = 'huawei';
        } else {
            $sms = $sms_fr;
            $enc = 0;
            $smsc = 'halys';
        }
        if ($sms == '')
            return 1;
        $ret = sendSMS($numero, $sms, $frm, $enc, $smsc);
        echo date('YmdHis')." SMS " . (($is_lang_ar == 'true') ? 'Arabic' : 'French') . " is send to $numero\n\r";
        return $ret;
    } else {
        $ret_ar = $ret_fr = 0;
        if ($sms_fr != '') {
            $ret_fr = sendSMS($numero, $sms_fr, $frm, 0, 'halys');
            echo date('YmdHis')." SMS French is send to $numero\n\r";
        }
        if ($sms_ar != '') {
            $ret_ar = sendSMS($numero, $sms_ar, $frm, 2, 'huawei');
            echo date('YmdHis')." SMS Arabic is send to $numero\n\r";
        }
        if ($ret_ar || $ret_fr)
            return 1;
        else
            return 0;
    }
}

function sendSMS($numero, $text, $from, $enc, $account = 'halys') {
    if ($text == '')
        return 1;
    global $SMSaccount, $SMSreponse;
    if ($enc == 2) {
        $enc.='&charset=UTF-8';
        $text = mb_convert_encoding($text, 'UTF-8');
    } else {
        $text = str_replace('ê', 'e', $text);
        $enc.='&charset=latin1';
        $text = mb_convert_encoding($text, 'LATIN1');
//        $text = iconv('UTF-8', 'LATIN1', $text);
    }
    $text = urlencode($text);
    if (!function_exists('curl_init'))
        return -1;
    else {
        $username = $SMSaccount[$account]['unsername'];
        $password = $SMSaccount[$account]['password'];
        $url = "http://localhost:13013/cgi-bin/sendsms?username=$username&password=$password&coding=$enc&from=$from&to=$numero&text=$text";
        $ch = curl_init();
        if (!$ch)
            die('curl_init n\'a pas marché');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $ret = $data;
        $data = explode(':', $data);
        if (isset($SMSreponse[$data[0]]))
            return 1;
        else
            return $ret;
    }
}

?>