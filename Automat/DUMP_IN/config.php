<?php
// $rep_chargement = '/data/cdrs/cdrtest/';
$rep_chargement = '/tim_DATA/dump/in/chargement/';
$rep_file_error = '/tim_DATA/dump/in/error/';
$rep_file_rejected = '/tim_DATA/dump/in/rejected/';
$rep_doublons = '/tim_DATA/dump/in/doublons/';
$rep_sauv = '/tim_arch/dump/in/';
$config = array();
$config['cdrName'] = 'din';
$config['separateur'] = '|';
$config['source'] = 'IN';
//$config['ocs'] = 34;
$config['pos']['numero'] = 0;
$config['pos']['imsi'] = 1;
$config['pos']['profil'] = 3;
$config['pos']['status'] = 4;
$config['pos']['balance'] = 6;
$config['pos']['is_lang_ar'] = 7;
$config['pos']['dt_active'] = 9;
$config['pos']['dt_active_stop'] = 10;
$config['pos']['dt_suspend_stop'] = 11;
$config['pos']['dt_disable_stop'] = 12;
$config['pfx']['numero'] = 222;
$config['inc']['status'] = -1;
$config['max'] = 12;

$config['code_ope'] = '2223';
$config['ln_int'] = 11;

function verifierNumeroNational($numero) {
    global $config;
    $ln_cd_op = strlen($config['code_ope']);
    if (strlen($numero) == $config['ln_int'] && substr($numero, 0, $ln_cd_op) == $config['code_ope'])
        return true;
    else
        return false;
}
?>