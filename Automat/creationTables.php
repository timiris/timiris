<?php
require_once 'connection.php';
// $cmpt = array("2001","2100","2500","2501","2503","2504","2506","2507","2508","2509","2511","4200","4500","5001","5002","5003","5004","5005","5008","5009","5010","5012","5100","5102","5103","5106");
// foreach($cmpt as $cmp){
	// echo $cmp;
	// $req_cration = 'create table data_appel_emis_valeur_'.$cmp.' as select * from data_appel_emis_valeur_2000';
	// $res_cration = $connection->query($req_cration);
	// $req3 = 'ALTER TABLE data_appel_emis_valeur_'.$cmp.' ADD PRIMARY KEY (numero)';
	// $res3 = $connection->query($req3);
// }

$services = array('10333', '20333', '12347', '110003', '4032105', '4039804', '4050505', '4053504', '4053508', '4057406', '4059703', '4065005', '4065414', '4065704', '4135105', '4135113', '4135120', '4135125', '4148605', '4149217', '4150603', '4157703', '4174805');
foreach($services as $service){
	$req_cration = 'create table data_service_'.$service.' as select * from data_service_4000002';
	$res_cration = $connection->query($req_cration);
	$req3 = 'ALTER TABLE data_service_'.$service.' ADD PRIMARY KEY (numero)';
	$res3 = $connection->query($req3);
}
echo count($services) .'Tables crées';
?>