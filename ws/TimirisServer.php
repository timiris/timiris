<?php
require 'fn.php';
require 'lib/nusoap.php';
$server = new nusoap_server();
$ns = 'urn:timirisWS';
$server->configureWSDL('timirisWS',$ns);
$server->register("getFidelitySolde", array('msisdn'=>'xsd:string', 'access'=>'xsd:string'), array('return'=>'xsd:int') ,$ns);
$server->register("subscribeFidelity", array('msisdn'=>'xsd:string', 'access'=>'xsd:string'), array('return'=>'xsd:int') ,$ns);
$server->register("adjustFidelitySolde", array('msisdn'=>'xsd:string', 'value'=>'xsd:int', 'access'=>'xsd:string'),
        array('return'=>'xsd:string') ,$ns);
$server->register("buyServiceFidelity", array('msisdn'=>'xsd:string', 'value'=>'xsd:int', 'service_id'=>'xsd:string', 'access'=>'xsd:string'),
        array('return'=>'xsd:string') ,$ns);
$server->service(file_get_contents("php://input"));
?>