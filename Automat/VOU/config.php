<?php
require_once '../config_prp.php';
$config['cdrName'] = 'vou';


$NewVal['max'] = 295;
$NewPos = array();
$NewPos['heure'] = 2;
$NewPos['profil'] = 274;
$NewPos['msisdn'] = 11;
$NewPos['serial'] = 219;
$NewPos['valeur'] = 246;                // Valeurs possibles sont : 225, 241, 246 et 293 
$NewPos['valeur_rechargee'] = 241;
$NewPos['access'] = 279;
$NewPos['canal'] = 279;
//$NewPos['transaction'] = 252;
$NewPos['cellid'] = 272;
$NewPos['status'] = 277;
$NewPos['dt_active_stop'] = 284;
$NewPos['dt_suspend_stop'] = 286;
$NewPos['dt_disable_stop'] = 288;
$NewPos['balance'] = 294;

$config['canal']['1'] = 'ivr';
$config['canal']['2'] = 'ivr';
$config['canal']['5'] = 'ussd';
$config['canal']['6'] = 'ussd';


$config['access']['1'] = 'ValeurFacial';
$config['access']['2'] = 'ValeurFacial';
$config['access']['5'] = 'ValeurFacial';
$config['access']['6'] = 'ValeurFacial';
$config['access']['54'] = 'Mechili';
$config['access']['1001'] = 'Autre';
$config['access']['7'] = 'WebSevices';

$old_mechili = array();
$old_mechili['50_300']['inf'] = 50;
$old_mechili['50_300']['sup'] = 300;
$old_mechili['301_500']['inf'] = 301;
$old_mechili['301_500']['sup'] = 500;
$old_mechili['501_1000']['inf'] = 501;
$old_mechili['501_1000']['sup'] = 1000;
$old_mechili['1001_2000']['inf'] = 1001;
$old_mechili['1001_2000']['sup'] = 2000;
$old_mechili['2001_5000']['inf'] = 2001;
$old_mechili['2001_5000']['sup'] = 5000;
$old_mechili['5001_10000']['inf'] = 5001;
$old_mechili['5001_10000']['sup'] = 10000;
$old_mechili['10001_50000']['inf'] = 10001;
$old_mechili['10001_50000']['sup'] = 2000000;


$new_mechili = array();
$new_mechili['1_9']= array('inf'=> 1, 'sup' => 9);
$new_mechili['10_19']= array('inf'=> 10, 'sup' => 19);
$new_mechili['20_29']= array('inf'=> 20, 'sup' => 29);
$new_mechili['30_49']= array('inf'=> 30, 'sup' => 49);
$new_mechili['50_99']= array('inf'=> 50, 'sup' => 99);
$new_mechili['100_199']= array('inf'=> 100, 'sup' => 199);
$new_mechili['200_299']= array('inf'=> 200, 'sup' => 299);
$new_mechili['300_499']= array('inf'=> 300, 'sup' => 499);
$new_mechili['500_999']= array('inf'=> 500, 'sup' => 999);
$new_mechili['1000_2000000']= array('inf'=> 1000, 'sup' => 2000000);

//$config['mechili'] = $old_mechili;

?>