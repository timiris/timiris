<?php

if (!isset($rep))
    $rep = '../';
require_once $rep . 'conn/connection.php';
require_once $rep . 'lib/tbLibelle.php';
$arrCode = array('ussd' => 'e3302f0abb4f919c3e2e6c3641b74815', 'web' => '4a3eef00b6802c9b73dfe5714c7b81ba');

//web_access_to_timiris
//fidelityAccessForUSSD
function getFidelitySolde($msisdn, $access) {
//    return 'Your parms is : '.$msisdn;
    global $connection, $arrCode;
    $src = array_search(md5($access), $arrCode);
//    if (!in_array(md5($access), $arrCode))
    if (!$src)
        return -3;
    $msisdn = addslashes($msisdn);
    $req = "select points_fidelite, has_fidelity from data_attribut where numero = '$msisdn'";
    $res = $connection->query($req);
    if ($res->rowCount()) {
        $ret = $res->fetch(PDO::FETCH_OBJ);
        if ($ret->has_fidelity)
            return $ret->points_fidelite;
        else
            return -2;
    } else
        return -1;
}

function subscribeFidelity($msisdn, $access) {
//    return 'Your parms is : '.$msisdn;
    global $connection, $arrCode;
    $src = array_search(md5($access), $arrCode);
//    if (!in_array(md5($access), $arrCode))
    if ($src != 'ussd')
        return -3;
    $soldeInit = 0;
    $msisdn = addslashes($msisdn);
    $res = $connection->query("select numero from data_attribut where has_fidelity = 1 and numero = '$msisdn'");
    if ($res->rowCount())
        return 2;

    $res = $connection->query("update data_attribut set has_fidelity = 1, points_fidelite = $soldeInit, dt_fidelity ='" . date('YmdHis') . "' where numero = '$msisdn'");
    if ($res->rowCount()) {
        return 1;
    } else
        return -1;
}

function buyServiceFidelity($msisdn, $valeur, $service_id, $access) {
    return adjustFidelitySolde($msisdn, $valeur, $access, $service_id);
}

function adjustFidelitySolde($msisdn, $valeur, $access, $service_id = 0) {
    //pwdFidelity
    $rp_log = '/tim_log/log_autre/fidelity_adjust.log';
//    $rp_log = 'C:/fidelity_adjust.log';
    global $connection, $libCorrespondance, $arrCode;
    $src = array_search(md5($access), $arrCode);
//    if (!in_array(md5($access), $arrCode))
    if (!$src)
        return -3;
    $valeur = (int) $valeur;
    if ($valeur <= 0)
        return -2;

    $res = $connection->query("SELECT points_fidelite FROM data_attribut WHERE numero = '$msisdn' and has_fidelity = 1");
    if ($res->rowCount()) {
        $solde = $res->fetch(PDO::FETCH_OBJ)->points_fidelite;
        if ($solde < $valeur)
            return 0;
    } else {
        return -1;
    }

    $ch = $chn = $chv = array();
    $dtj = date('Y-m-d');
    $dta = substr($dtj, 0, 4);
    $dtm = substr($dtj, 0, 7);
    $dta = array_search($dta, $libCorrespondance);
    if ($dta) {
        $chn[] = "$dta = $dta + 1";
        $chv[] = "$dta = $dta + $valeur";
    }
    $dtm = array_search($dtm, $libCorrespondance);
    if ($dtm) {
        $chn[] = "$dtm = $dtm + 1";
        $chv[] = "$dtm = $dtm + $valeur";
    }
    $dtj = array_search($dtj, $libCorrespondance);
    if ($dtj) {
        $chn[] = "$dtj = $dtj + 1";
        $chv[] = "$dtj = $dtj + $valeur";
    }

    $connection->beginTransaction();
    $res = $connection->query("select numero from data_point_fidelite_nombre_consommation WHERE numero = '$msisdn'");
    if (!$res->rowCount()) {
        $connection->query("insert into data_point_fidelite_nombre_consommation (numero) values ('$msisdn')");
        $connection->query("insert into data_point_fidelite_valeur_consommation (numero) values ('$msisdn')");
        $connection->query("insert into data_point_fidelite_nombre_consommation_ussd (numero) values ('$msisdn')");
        $connection->query("insert into data_point_fidelite_valeur_consommation_ussd (numero) values ('$msisdn')");
        $connection->query("insert into data_point_fidelite_nombre_consommation_web (numero) values ('$msisdn')");
        $connection->query("insert into data_point_fidelite_valeur_consommation_web (numero) values ('$msisdn')");
    }
    if (count($chn)) {
        $connection->query("UPDATE data_point_fidelite_nombre_consommation SET " . implode(', ', $chn) . " WHERE numero = '$msisdn'");
        $connection->query("UPDATE data_point_fidelite_valeur_consommation SET " . implode(', ', $chv) . " WHERE numero = '$msisdn'");
        $connection->query("UPDATE data_point_fidelite_nombre_consommation_$src SET " . implode(', ', $chn) . " WHERE numero = '$msisdn'");
        $connection->query("UPDATE data_point_fidelite_valeur_consommation_$src SET " . implode(', ', $chv) . " WHERE numero = '$msisdn'");
        $connection->query("UPDATE data_attribut SET points_fidelite = points_fidelite - $valeur WHERE numero = '$msisdn'");
    }
    if ($service_id) {
        $searchTb = $connection->query("SELECT * FROM pg_catalog.pg_tables where tablename = 'data_point_fidelite_nombre_consommation_$service_id'");
        if (!$searchTb->rowCount()) {
            createTable("data_point_fidelite_nombre_consommation_$service_id", $connection);
            createTable("data_point_fidelite_valeur_consommation_$service_id", $connection);
        }
        $rqVerif = $connection->query("select numero from data_point_fidelite_nombre_consommation_$service_id WHERE numero = '$msisdn'");
        if (!$rqVerif->rowCount()) {
            $connection->query("insert into data_point_fidelite_nombre_consommation_$service_id (numero) values ('$msisdn')");
            $connection->query("insert into data_point_fidelite_valeur_consommation_$service_id (numero) values ('$msisdn')");
        }
        if (count($chn)) {
            $connection->query("UPDATE data_point_fidelite_nombre_consommation_$service_id SET " . implode(', ', $chn) . " WHERE numero = '$msisdn'");
            $connection->query("UPDATE data_point_fidelite_valeur_consommation_$service_id SET " . implode(', ', $chv) . " WHERE numero = '$msisdn'");
        }
    }
    if ($connection->commit()) {
        if (is_file($rp_log)) {
            $fp = fopen($rp_log, 'a');
            $str = date('YmdHis') . " : $src : $msisdn : $valeur\r\n";
            fputs($fp, $str);
            fclose($fp);
        }
        return 1;
    } else {
        $connection->rolBack();
        return -4;
    }
}

?>