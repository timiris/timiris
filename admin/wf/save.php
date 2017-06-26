<?php

if (!isset($_SESSION))
    session_start();
if (!isset($rep))
    $rep = '../../';
require_once $rep . "fn_security.php";
check_session();
if (isset($_POST['wf']) && !empty($_POST['wf'])) {
    try {
        require_once $rep . 'conn/connection.php';
        $connection->beginTransaction();
        $retour = array();
        $retour['exec'] = 0;
        $retour['message'] = '';
        $wf = json_decode($_POST["wf"], true);
        ksort($wf);
        $key = $val = 0;
        $arr = array();
        foreach ($wf as $v) {
            $key = $val;
            $val = $v;
            if ($key)
                $arr[$key] = $val;
            else
                $pr = $val;
        }
        $connection->query('delete from ref_wf where profil = ' . $pr);
        $connection->query("insert into ref_wf values (" . $pr . ", '" . json_encode($arr) . "')");
        if($connection->Commit()){
            $retour['exec'] = 1;
        }
    } catch (PDOException $e) {
        $connection->rollback();
        $retour['exec'] = 0;
        $retour['message'] = $e->getMessage();
    }
    echo json_encode($retour);
}
?>
