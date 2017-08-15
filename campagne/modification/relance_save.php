<?php

if (!isset($_SESSION))
    session_start();
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
$ret = array('exec' => 1, 'message' => '');
if (isset($_POST['parms'])) {
    require_once $rep . "conn/connection.php";
    require_once $rep . "defs.php";
    $parms = json_decode($_POST['parms'], true);
    $idCmp = (int) $parms['idCmp'];
    $cmp = $connection->query('SELECT * FROM app_campagne WHERE etat = ' . CMP_ENCOURS . ' AND id = ' . $idCmp);
    if ($cmp->rowCount()) {
        try {
            $broadcast = $parms['broadcast'];
            $smsAr = str_replace("'", "''", $parms['smsTeasignAr']);
            $smsFr = str_replace("'", "''", $parms['smsTeasignFr']);
            $dtRelance = $parms['dt_relance'];
            $dtRelance = str_replace('-', '', $dtRelance);
            $dtRelance = str_replace(' ', '', $dtRelance);
            $dtRelance = str_replace(':', '', $dtRelance);
            $connection->query("UPDATE app_campagne SET broadcast = $broadcast, dt_relance = '$dtRelance', nbr_relance = nbr_relance +1, sms_ar = '$smsAr', sms_fr ='$smsFr' WHERE id = $idCmp");
            $ret['exec'] = 1;
        } catch (Exception $e) {
            $ret['message'] = $e->getMessage();
        }
    }
}
echo json_encode($ret);
