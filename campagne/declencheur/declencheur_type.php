<?php
if(!isset($rep))
    $rep = '../../';
if(!isset($_SESSION)) session_start();
require_once $rep."fn_security.php";
check_session();
$bc = array('cumule_j', 'cumule_m', 'evenement');
if(isset($_POST['type'])){
    $type = $_POST['type'];
    if($type == 'fidelite')
        require_once 'bonus_fidelite.php';
    if(in_array($type, $bc))
        require_once 'bonus_conditionne.php';
}
?>