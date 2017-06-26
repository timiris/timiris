<?php
require_once '../config_prp.php';
$config['cdrName'] = 'data';

$NewVal['max'] = 432;
$NewPos['heure'] = 5;
$NewPos['msisdn'] = 387;
$NewPos['imsi'] = 390;
$NewPos['volume'] = 398;
$NewPos['profil'] = 414;
$NewPos['ResultCode'] = 20;
$NewPos['RoamState'] = 417;
$NewPos['status'] = 431;
$NewPos['cout'] = 33;
$NewPos['balance'] = 47;

// ici on doit déclarer tous les compteurs qui permet de la data (compteurs data et monnaie  )
$config['agreg']['2000'] = 'allmonnaie';
$config['agreg']['4500'] = 'alldata';
$config['agreg']['5014'] = 'alldata';
$config['agreg']['5015'] = 'alldata';
$config['agreg']['5016'] = 'alldata';
$config['agreg']['5018'] = 'alldata';
$config['agreg']['5103'] = 'alldata';

$config['agreg']['5030'] = 'timdata';
$config['agreg']['5031'] = 'timdata';
$config['agreg']['5032'] = 'timdata';
$config['agreg']['5033'] = 'timdata';


?>