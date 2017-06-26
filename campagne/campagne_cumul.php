<?php

$rep = '../';
try {
    if (isset($_GET['periode']))
        $periode = $_GET['periode'];
    else {
        exit();
    }
    require_once $rep . "conn/connection.php";
    require_once $rep . "ciblage/fn/fn_generateRequete.php";
    require_once $rep . "ciblage/fn/fn_generateArrayParams.php";
    require_once $rep . "ciblage/fn/fn_getDateRel.php";
    require_once $rep . "nameTables.php";
    require_once $rep . "lib/tbLibelle.php";
    require_once $rep . "defs.php";
    require_once "fn/fn_generateArrayGroupe.php";
    require_once $rep . "Automat/config_prp.php";



    $arrReq = $bns_dec = array();
    $tbTables = array(2 => 'data_appel_emis', 3 => 'data_appel_recu', 4 => 'data_sms_emis',
        5 => 'data_recharge', 6 => 'data_mgr', 7 => 'data_data', 8 => 'data_service', 12 => 'data_consommation');
    $convertUnitBonusCumule = array(1000 => 1, 36 => 100, 26 => 60, 25 => 1048576);

    $req_bns = "select acb.* from app_campagne_bonus acb
        JOIN app_campagne ac on ac.id = acb.fk_id_campagne and ac.etat = " . CMP_ENCOURS . " and ac.type_bonus = 'cumule_" . $periode . "'";
    $res_bns = $connection->query($req_bns);
    if ($res_bns->rowCount()) {
        while ($li_bns = $res_bns->fetch(PDO::FETCH_OBJ)) {
            if (!isset($bns_dec[$li_bns->fk_id_campagne]))
                $bns_dec[$li_bns->fk_id_campagne] = array();
            if (!isset($bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe]))
                $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe] = array();
            $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe][$li_bns->id] = array();
            $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe][$li_bns->id]['type_bonus'] = $li_bns->type_bonus;
            $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe][$li_bns->id]['nature'] = $li_bns->nature;
            $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe][$li_bns->id]['code'] = $li_bns->code_bonus;
            $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe][$li_bns->id]['valeur'] = $li_bns->valeur;
            $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe][$li_bns->id]['ch_ref'] = $li_bns->ch_ref;
            $bns_dec[$li_bns->fk_id_campagne][$li_bns->fk_id_groupe][$li_bns->id]['unite'] = $li_bns->unite;
        }
    }else {
        echo "Aucune campagne de cumul " . (($periode == 'j') ? "journaliére" : 'mensuelle') . "\n\r";
        exit();
    }

    $arr_valorisation = array();
    $reqList = 'select distinct code_cmpt, valorisation from ref_compteurs';
    $resList = $connection->query($reqList);
    while ($li_list = $resList->fetch(PDO::FETCH_OBJ))
        $arr_valorisation[$li_list->code_cmpt] = $li_list->valorisation;

    $connection->query('BEGIN');
    $req = "SELECT acg.* FROM app_campagne ac
        JOIN app_campagne_groupe acg on ac.id = acg.fk_id_campagne
        WHERE ac.etat = " . CMP_ENCOURS . " and ac.type_bonus = 'cumule_" . $periode . "'";
    $result = $connection->query($req);
    //$periode = 'j';
    if ($periode == 'j') {
        $dtj = strtotime(date("Ymd"));
        $dt = date("Y-m-d", strtotime("-1 days", $dtj));
    } else {
        $dtm = strtotime(date("Ymd"));
        $dt = date("Y-m", strtotime("first day of last month", $dtm));
    }
    $arrLastCmp = array();
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $idGrp = $ligne->id;
        $idCmp = $ligne->fk_id_campagne;
        $nature = $ligne->fk_id_nature;
        $param = fn_generateArrayGroupe($idGrp, $nature, $periode, $dt, $connection);
        $tables = generateArrayParams($param);
//        print_r($tables);
        $rtCrit = generateReqCriteres($tables['G1'], 'and');
        $reqGroupe = generateReqGroupe('and', $rtCrit);
        if (!isset($arr_glb_bonus[$idCmp]))
            $arr_glb_bonus[$idCmp] = array();
        if (isset($bns_dec[$idCmp][$idGrp])) {
            foreach ($bns_dec[$idCmp][$idGrp] as $idBns => $bns) {
                if ($bns['type_bonus'] == 2) {
                    $reqi = 'SELECT tbPP.numero, tbPP.' . array_search($dt, $libCorrespondance) . ' cumule 
                    FROM ' . $tbTables[$nature] . '_' . $bns['ch_ref'] . ' tbPP
                        JOIN (' . $reqGroupe . ') rqGr on rqGr.numero = tbPP.numero
                        JOIN app_campagne_cible acc on acc.numero tbPP.numero and acc.fk_id_campagne = ' . $idCmp;
//                    print $reqi . '<br>';
                    $resi = $connection->query($reqi);
                    while ($li = $resi->fetch(PDO::FETCH_OBJ)) {
                        $msisdn = $li->numero;
                        $valeur = $li->cumule;
                        if (!isset($arr_glb_bonus[$idCmp][$msisdn]))
                            $arr_glb_bonus[$idCmp][$msisdn] = array();
                        if (!isset($arr_glb_bonus[$idCmp][$msisdn][$idGrp]))
                            $arr_glb_bonus[$idCmp][$msisdn][$idGrp] = array();

                        $valeur = (int) ($bns['valeur'] * $valeur / 100);
                        $valeur -= $valeur % $convertUnitBonusCumule[$bns['nature']];
                        if ($valeur > 0) {
                            $valorisation = ($valeur / $convertUnitBonusCumule[$bns['nature']]) * $arr_valorisation[$bns['code']];
                            $arr_glb_bonus[$idCmp][$msisdn][$idGrp][] = array('idBns' => $idBns, 'nature' => $bns['nature'],
                                'code' => $bns['code'], 'valeur' => $valeur, 'dt_action' => $dt, 'valorisation' => $valorisation);
                        }
                    }
                } else {    //Bonus Libre
//                    $reqi = $reqGroupe;
                    $reqi = "SELECT acc.numero FROM app_campagne_cible acc
                        JOIN ($reqGroupe)  rqGr on rqGr.numero = acc.numero and acc.fk_id_campagne = $idCmp";
//                    print $reqi . '<br>';
                    $resi = $connection->query($reqi);
                    $valeur = $bns['valeur'] * $bns['unite'];
                    $valorisation = $bns['valeur'] * $arr_valorisation[$bns['code']];
                    while ($li = $resi->fetch(PDO::FETCH_OBJ)) {
                        $msisdn = $li->numero;
                        if (!isset($arr_glb_bonus[$idCmp][$msisdn]))
                            $arr_glb_bonus[$idCmp][$msisdn] = array();
                        if (!isset($arr_glb_bonus[$idCmp][$msisdn][$idGrp]))
                            $arr_glb_bonus[$idCmp][$msisdn][$idGrp] = array();
                        $arr_glb_bonus[$idCmp][$msisdn][$idGrp][] = array('idBns' => $idBns, 'nature' => $bns['nature'],
                            'code' => $bns['code'], 'valeur' => $valeur, 'dt_action' => $dt, 'valorisation' => $valorisation);
                    }
                }
            }
        }
        $idGrp = 0;
        if (isset($bns_dec[$idCmp][$idGrp]) && !in_array($idCmp, $arrLastCmp)) {
            foreach ($bns_dec[$idCmp][$idGrp] as $idBns => $bns) {
                $valeur = $bns['valeur'] * $bns['unite'];
                $valorisation = $bns['valeur'] * $arr_valorisation[$bns['code']];
                foreach ($arr_glb_bonus[$idCmp] as $msisdn => $kkk) {
                    if (!isset($arr_glb_bonus[$idCmp][$msisdn][$idGrp]))
                        $arr_glb_bonus[$idCmp][$msisdn][$idGrp] = array();
                    $arr_glb_bonus[$idCmp][$msisdn][$idGrp][] = array('idBns' => $idBns, 'nature' => $bns['nature'],
                        'code' => $bns['code'], 'valeur' => $valeur, 'dt_action' => $dt, 'valorisation' => $valorisation);
                }
            }
            $arrLastCmp[] = $idCmp;
        }
//        print_r($arr_glb_bonus[$idCmp]);
        if (isset($arr_glb_bonus[$idCmp]) && count($arr_glb_bonus[$idCmp]))
            execute_all_bonus($arr_glb_bonus, $connection, str_replace('-', '', $dt . '01'));
        $arr_glb_bonus = array();
    }

    if (!$connection->query('COMMIT'))
        throw('COMMIT impossible');
} catch (PDOException $e) {
    $connection->query('ROLLBACK');
    echo $e->getMessage();
    print_r($e);
}
?>