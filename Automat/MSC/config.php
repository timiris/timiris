<?php

require_once '../config_prp.php';
$rep_chargement = $repGlobal . '/tim_DATA/cdrs/chargement/msc/';
$rep_doublons = $repGlobal . '/tim_DATA/cdrs/doublons/msc/';
$rep_file_rejected = $repGlobal . '/tim_DATA/cdrs/rejected/msc/';
$rep_file_error = $repGlobal . '/tim_DATA/cdrs/error/msc/';
$rep_sauv = $repGlobal . '/tim_arch/sauvegarde/msc/';
$rep_log = $repGlobal . '/tim_log/log_NewEvent/';
$rep_log_ignored = $repGlobal . '/tim_log/log_ignored/msc/';

$config['nb_files'] = 1;
$config['nbrFichierCharge'] = 20;
$config['cdrName'] = 'b';
$config['msc'] = '2223';
$config['A0'] = 1;
$config['A1'] = 1;
$config['A2'] = 1;
$config['A3'] = 1;
$config['A4'] = 1;
$config['A5'] = 1;
$config['A6'] = 1;
$config['A7'] = 1;
$config['BF'] = 2;

$config['tag_A1'] = 'MTC';
$config['tag_A7'] = 'SMT';
$config['tag_A0'] = 'MOC';
$config['tag_A6'] = 'SMO';
$config['tag_BF'] = 'BF';


$config['MTC']['heure'] = '94';
$config['MTC']['msisdn'] = '83';
$config['MTC']['msisdn_autre'] = '84';
$config['MTC']['imsi'] = '81';
$config['MTC']['msc'] = '9F22';
$config['MTC']['cellid'] = '9F813C';
$config['MTC']['duree'] = '96';

$config['SMT']['heure'] = '88';
$config['SMT']['msisdn'] = '84';
$config['SMT']['msisdn_autre'] = '9F8149';
$config['SMT']['imsi'] = '82';
$config['SMT']['msc'] = '86';
$config['SMT']['cellid'] = '9F813C';
$config['SMT']['serviceCentre'] = '81';

//require_once 'format.old.php';
require_once 'format.new.php';


?>