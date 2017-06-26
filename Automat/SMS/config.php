<?php
require_once '../config_prp.php';
$config['cdrName'] = 'sms';

$NewVal['max'] = 429;
$NewPos['heure'] = 5;
$NewPos['msisdn'] = 387;
$NewPos['msisdn_autre'] = 388;
$NewPos['imsi'] = 389;
$NewPos['type_sms'] = 393;
$NewPos['msc'] = 395;
$NewPos['cellid'] = 396;
$NewPos['smsid'] = 402;          //************** On a pris le sendResult au lieu de smsid
$NewPos['profil'] = 409;
$NewPos['RoamNetworkCode'] = 420;   // **** a vérifier, si sms roam sont générés
$NewPos['status'] = 428;
$NewPos['balance'] = 47;
$NewPos['compteur'] = 105;
//$NewPos['cout'] = 46;
$NewPos['cout'] = 107;
$NewPos['oper_type'] = 109;
//******************************************

$config['agreg']['2000'] = 'allmonnaie';
$config['agreg']['4200'] = 'allsms';
$config['agreg']['5102'] = 'allsms';
$config['agreg']['5106'] = 'allsms';


$config['agreg']['5034'] = 'timsms';
$config['agreg']['5035'] = 'timsms';
$config['agreg']['5036'] = 'timsms';

// Compteurs a exclus, car il s'agit du bonus donné sur la consommation SMS
$config['cmpt_exclus'] = array("4500", "5014", "5015", "5016", "5103", "5010", "5003", "5002", "5012", "5001", "5100", "5009",
"5008", "5005", "5004","2500","2509","2001","2508","2507","2506","2503","2504","2100","2511","2501");
?>