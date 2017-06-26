<?php
require_once "../fn_security.php";
check_session(); 
$ret = array();
$ret['exec'] = 0;
// sleep(1);
if (isset($_SESSION['ciblage']) && ($_SESSION['ciblage'] == 'en_cours'))
    $ret['exec'] = 1;
echo json_encode($ret);
?>