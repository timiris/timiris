<?php
if(!isset($rep))
    $rep = '../';
require_once $rep."fn_security.php";
check_session(); 
require "ciblage_entete.php";
require "ciblage_corps.php";
?>