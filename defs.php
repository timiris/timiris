<?php
define('INDICATIF', '222');
define('PQ_OPERATOR', '2223');
//define('PQ_NAT', array('2224', '2222'));
define('LNF_NAT',8);
define('LNF_INT',11);
define('PROFIL_ADMIN',1);

define('CMP_EDTION', 1);
define('CMP_SOUMISE', 2);
define('CMP_REJETEE', 3);
define('CMP_ATTENTE', 4);
define('CMP_ENCOURS', 5);
define('CMP_SUSPENDUE', 6);
define('CMP_ARRETEE', 7);
define('CMP_TERMINEE', 8);

define('BNS_SERVICE', 17);
define('BNS_FIDELITY', 1000);
define('BNS_MONNAIE', 36);
define('BNS_TIM_MONNAIE', 92);
define('BNS_TIME', 26);
define('BNS_TIM_TIME', 93);
define('BNS_SMS', 24);
define('BNS_TIM_SMS', 94);
define('BNS_DATA', 25);
define('BNS_TIM_DATA', 95);

define('NATURE_ATTRIBUT', 1);   //


$unitBns = array();
$unitBns[BNS_SERVICE] = array('lib'=>'Activation', 'div'=>1);
$unitBns[BNS_FIDELITY] = array('lib'=>'Point', 'div'=>1);
$unitBns[BNS_MONNAIE] = array('lib'=>'UM', 'div'=>100);
$unitBns[BNS_TIM_MONNAIE] = array('lib'=>'UM', 'div'=>100);
$unitBns[BNS_TIME] = array('lib'=>'Min', 'div'=>60);
$unitBns[BNS_TIM_TIME] = array('lib'=>'Min', 'div'=>60);
$unitBns[BNS_SMS] = array('lib'=>'SMS', 'div'=>1);
$unitBns[BNS_TIM_SMS] = array('lib'=>'SMS', 'div'=>1);
$unitBns[BNS_DATA] = array('lib'=>'MO', 'div'=>1048576);
$unitBns[BNS_TIM_DATA] = array('lib'=>'MO', 'div'=>1048576);

$MinMesure = array(BNS_MONNAIE=>101, BNS_TIME=>1, BNS_DATA=>2, BNS_SMS=>6, BNS_TIM_MONNAIE=>101, BNS_TIM_TIME=>1, BNS_TIM_DATA=>2, BNS_TIM_SMS=>6);
?>
