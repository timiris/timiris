<?php
require_once '../config_prp.php';
$config['cdrName'] = 'rec';

$NewVal['type_appel'] = 1;
$NewVal['max'] = 432;
$NewPos['heure'] = 5;
$NewPos['msisdn'] = 387;
$NewPos['msisdn_appele'] = 388;
$NewPos['imsi'] = 389;
$NewPos['imsi1'] = 390;
$NewPos['msisdn_renvoi'] = 391;
$NewPos['type_appel'] = 393;
$NewPos['CallForwardIndicator'] = 394;
$NewPos['msc'] = 395;
$NewPos['cellid'] = 396;
$NewPos['msc1'] = 14;                       //***** non trouvé
$NewPos['cellid1'] = 15;                    //***** non trouvé
$NewPos['duree'] = 23;
$NewPos['dureeFacturee'] = 24;
$NewPos['profil'] = 411;
$NewPos['status'] = 430;
$NewPos['montant'] = 46;
$NewPos['balance'] = 47;

$NewPos['compteur1'] = 105;
$NewPos['cout1'] = 107;
$NewPos['compteur2'] = 117;
$NewPos['cout2'] = 119;
$NewPos['compteur3'] = 129;
$NewPos['cout3'] = 131;
$NewPos['compteur4'] = 141;
$NewPos['cout4'] = 143;
$NewPos['compteur5'] = 153;
$NewPos['cout5'] = 155;
$NewPos['compteur6'] = 165;
$NewPos['cout6'] = 167;
$NewPos['compteur7'] = 177;
$NewPos['cout7'] = 179;
$NewPos['compteur8'] = 189;
$NewPos['cout8'] = 191;
$NewPos['compteur9'] = 201;
$NewPos['cout9'] = 203;
$NewPos['compteur10'] = 213;
$NewPos['cout10'] = 215;

//#######################################################

$config['agreg']['2000'] = 'allmonnaie';
$config['agreg']['2001'] = 'allmonnaie';
$config['agreg']['2100'] = 'allmonnaie';
$config['agreg']['2500'] = 'allmonnaie';
$config['agreg']['2501'] = 'allmonnaie';
$config['agreg']['2503'] = 'allmonnaie';
$config['agreg']['2504'] = 'allmonnaie';
$config['agreg']['2506'] = 'allmonnaie';
$config['agreg']['2507'] = 'allmonnaie';
$config['agreg']['2508'] = 'allmonnaie';
$config['agreg']['2509'] = 'allmonnaie';
$config['agreg']['2511'] = 'allmonnaie';
$config['agreg']['2512'] = 'allmonnaie';
$config['agreg']['2514'] = 'allmonnaie';
$config['agreg']['5001'] = 'alltime';
$config['agreg']['5002'] = 'alltime';
$config['agreg']['5003'] = 'alltime';
$config['agreg']['5004'] = 'alltime';
$config['agreg']['5005'] = 'alltime';
$config['agreg']['5008'] = 'alltime';
$config['agreg']['5009'] = 'alltime';
$config['agreg']['5010'] = 'alltime';
$config['agreg']['5012'] = 'alltime';
$config['agreg']['5019'] = 'alltime';
$config['agreg']['5020'] = 'alltime';
$config['agreg']['5021'] = 'alltime';
$config['agreg']['5022'] = 'alltime';
$config['agreg']['5023'] = 'alltime';
$config['agreg']['5100'] = 'alltime';


$config['agreg']['2516'] = 'timmonnaie';
$config['agreg']['2517'] = 'timmonnaie';
$config['agreg']['2518'] = 'timmonnaie';

$config['agreg']['5027'] = 'timtime';
$config['agreg']['5028'] = 'timtime';
$config['agreg']['5029'] = 'timtime';

$config['pays']['223'] = 'pays1';
$config['pays']['221'] = 'pays2';
$config['pays']['212'] = 'pays3';
$config['pays']['244'] = 'pays4';
$config['pays']['33'] = 'pays5';

// Compteurs a exclus, car il s'agit du bonus donné sur la consommation voix
$config['cmpt_exclus'] = array("5103","4500", "5102", "4200", "5106");
?>