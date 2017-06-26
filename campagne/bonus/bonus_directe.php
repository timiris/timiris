<?php

$bns_dec = array();
$res_bns = $connection->query("select * from app_campagne_bonus acb where fk_id_campagne = $idCmp");
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
}
$req = "SELECT numero from app_campagne_cible WHERE fk_id_campagne = $idCmp";
$result = $connection->query($req);
$dtj = date("YmdHis");
$idGrp = 0;
$arr_glb_bonus = array();
$arr_glb_bonus[$idCmp] = array();
while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
    $msisdn = $ligne->numero;
    if (!isset($arr_glb_bonus[$idCmp][$msisdn]))
        $arr_glb_bonus[$idCmp][$msisdn] = array();
    foreach ($bns_dec[$idCmp][$idGrp] as $idBns => $bns) {
        $valeur = $bns['valeur'] * $bns['unite'];
        $valorisation = $bns['valeur'] * $arr_valorisation[$bns['code']];
        if (!isset($arr_glb_bonus[$idCmp][$msisdn][$idGrp]))
            $arr_glb_bonus[$idCmp][$msisdn][$idGrp] = array();
        $arr_glb_bonus[$idCmp][$msisdn][$idGrp][] = array('idBns' => $idBns, 'nature' => $bns['nature'],
            'code' => $bns['code'], 'valeur' => $valeur, 'dt_action' => $dtj, 'valorisation' => $valorisation);
    }
}
if (isset($arr_glb_bonus[$idCmp]) && count($arr_glb_bonus[$idCmp]))
    execute_all_bonus($arr_glb_bonus, $connection, $dtj);
?>