<?php

$urlAdjust = 'http://10.86.7.12:4884/services/CBSInterfaceAccountMgrService?wsdl';
$urlAppendant = 'http://10.86.7.12:4884/services/CBSInterfaceBusinessMgrService?wsdl';
$wsClientAdjust = new soapClient($urlAdjust, array('trace' => 1));
$wsClientAppendante = new soapClient($urlAppendant, array('trace' => 1));
date_default_timezone_set('Africa/Dakar');
//var_dump(fn_QueryBalance($wsClient));
function fn_QueryBalance($wsClient) {
    return $wsClient->__getFunctions();
}

function fn_TestAdjust($wsClient) {
    $msisdn = '38773477';
    $account = 2516;
    $valeur = 200;
    $MinMesure = 6;
    return fn_AdjustAccount($wsClient, $msisdn, $account, $valeur, $MinMesure);
}
//print_r(fn_TestAdjust($wsClientAdjust));

function fn_AdjustAccount($wsClient, $msisdn, $account, $valeur, $MinMesure) {
    $validity = array(2516=>1,2517=>3,2518=>7,5027=>1,5028=>3,5029=>7,5030=>0,5031=>1,5032=>3,5033=>7,5034=>1,5035=>3,5036=>7);
    $msisdn = substr($msisdn, -8);
    $ModifyAcctFee = array('AccountType' => $account, 'CurrAcctChgAmt' => $valeur, 'MinMeasureId' => $MinMesure);
    if(in_array($account, $validity)){
        if($validity[$account]){
            $dateJ = strtotime(date("YmdHis"));
            $ExpireTime = date("YmdHis", strtotime("+".$validity[$account]." days", $dateJ));;
        }
        else {
            $ExpireTime = date('Ymd').'235959';
        }
        $ModifyAcctFee['ValidityType'] = 0;
        $ModifyAcctFee['ExpireTime'] = $ExpireTime;
    }
    $params = array('AdjustAccountRequest' =>
        array('AdditionnalInfo' => 'Timiris Test Bonus',
            'SubscriberNo' => $msisdn,
            'OperateType' => 2,
            'ModifyAcctFeeList' => array('ModifyAcctFee' => $ModifyAcctFee)
        ),
        'RequestHeader' => array('CommandId' => 'AdjustAccount', 'Version' => 1, 'TransactionId' => 1,
            'SequenceId' => 1, 'RequestType' => 'Event', 'SerialNo' => date('YmdHis') . rand(100, 999))
    );
    
    $response = $wsClient->AdjustAccount($params);
    if ($response->ResultHeader->ResultCode == "405000000")
        return true;
    else {
        return false;
    }
}

function fn_ActivateService($wsClient, $msisdn, $service) {
    $msisdn = substr($msisdn, -8);
    $params = array('SubscribeAppendantProductRequest' =>
        array('SubscriberNo' => $msisdn, 'HandlingChargeFlag' => 0, 'Product' => array('Id' => $service, 'ValidMode' => 4050000)),
        'RequestHeader' => array('CommandId' => 'SubscribeAppendantProduct', 'Version' => 1, 'TransactionId' => 1,
            'SequenceId' => 1, 'RequestType' => 'Event', 'SerialNo' => date('YmdHis') . rand(100, 999))
    );
    $response = $wsClient->SubscribeAppendantProduct($params);
//    print_r($response);
    if ($response->ResultHeader->ResultCode == "405000000")
        return true;
    else {
        return false;
    }
}

function fnFidelityPoint($connection, $msisdn, $valeur, $dtj, $exist) {
    global $libCorrespondance;
    $ch = $chn = $chv = array();
    $dta = substr($dtj, 0, 4);
    $dtm = $dta . '-' . substr($dtj, 4, 2);
    $dtj = $dtm . '-' . substr($dtj, 6, 2);
    if ($exist) {
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
        $reqn = "UPDATE data_point_fidelite_nombre_attribution set " . implode(', ', $chn) . " where numero ='$msisdn'";
        $reqv = "UPDATE data_point_fidelite_valeur_attribution set " . implode(', ', $chv) . " where numero ='$msisdn'";
    } else {
        $dta = array_search($dta, $libCorrespondance);
        if ($dta) {
            $ch[] = $dta;
            $chn[] = 1;
            $chv[] = $valeur;
        }
        $dtm = array_search($dtm, $libCorrespondance);
        if ($dtm) {
            $ch[] = $dtm;
            $chn[] = 1;
            $chv[] = $valeur;
        }
        $dtj = array_search($dtj, $libCorrespondance);
        if ($dtj) {
            $ch[] = $dtj;
            $chn[] = 1;
            $chv[] = $valeur;
        }
        $reqn = "insert into data_point_fidelite_nombre_attribution (numero, " . implode(',', $ch) . ") values ('$msisdn', " . implode(',', $chn) . ")";
        $reqv = "insert into data_point_fidelite_valeur_attribution (numero, " . implode(',', $ch) . ") values ('$msisdn', " . implode(',', $chv) . ")";
    }
    $connection->beginTransaction();
    $res = $connection->query($reqn);
    $res = $connection->query($reqv);
    $reqv = "UPDATE data_attribut set points_fidelite = points_fidelite + $valeur where numero ='$msisdn'";
    $res = $connection->query($reqv);
    if ($connection->commit())
        return true;
    else {
        $connection->rolBack();
        return false;
    }
}

?>