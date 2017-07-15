<?php
$grp_dec = $bns_dec = $cmp_lim = array();
$dateLimCmpTer = strtotime(date("YmdHis"));
$dateLimCmpTer = date("YmdHis", strtotime("-1 days", $dateLimCmpTer));
$localisation = false;
$arr_localisation = $arr_dt_cmp = array();
$req_dec = 'select acg.fk_id_nature nature_dec, ac.id idCmp, acd.*, ac.dt_lancement_reelle, ac.dt_fin
            from app_campagne_groupe acg
            join app_campagne_declencheur acd on acg.id = acd.fk_id_groupe
            join app_campagne ac on acg.fk_id_campagne = ac.id 
                and (ac.etat = ' . CMP_ENCOURS . ' or (etat = ' . CMP_TERMINEE . ' and dt_fin_relle >'."'$dateLimCmpTer'".')) 
                and ac.type_bonus = ' . "'evenement'" . '
            where acg.fk_id_nature in (' . $decNatureCdrName[$config['cdrName']] . ')';
$res_dec = $connection->query($req_dec);
if ($res_dec->rowCount()) {
    $req_crp = 'select type, code, cdr from ref_event';
    $res_crp = $connection->query($req_crp);
    while ($li_crp = $res_crp->fetch(PDO::FETCH_OBJ)) {
        $event_corsp[$li_crp->type . '_' . $li_crp->code] = $li_crp->cdr;
    }

    while ($li_grp = $res_dec->fetch(PDO::FETCH_OBJ)) {
        if ($li_grp->code_declencheur == 'localisation')
            $localisation = true;
        if (!isset($grp_dec[$li_grp->idcmp])) {
            $grp_dec[$li_grp->idcmp] = array();
            $dtStart = str_replace(str_split(' :-'), '', $li_grp->dt_lancement_reelle);
            $dtStop = str_replace(str_split(' :-'), '', $li_grp->dt_fin);
            $arr_dt_cmp[$li_grp->idcmp] = array('dtStart' => $dtStart, 'dtStop' => $dtStop . '00');
        }

        if (!isset($grp_dec[$li_grp->idcmp][$li_grp->fk_id_groupe]))
            $grp_dec[$li_grp->idcmp][$li_grp->fk_id_groupe] = array();
        if ($li_grp->unite > 1) {
            $arrVals = explode('|', $li_grp->valeur);
            foreach ($arrVals as $key => $val)
                $arrVals[$key] = $li_grp->unite * $val;
            $li_grp->valeur = implode("|", $arrVals);
        }
        $grp_dec[$li_grp->idcmp][$li_grp->fk_id_groupe][$li_grp->id] =
                array(
                    'code' => $li_grp->code_declencheur,
                    'cdrField' => $event_corsp[$li_grp->fk_id_td_event . '_' . $li_grp->code_declencheur],
                    'operateur' => $li_grp->operateur,
                    'valeur' => $li_grp->valeur,
                    'nature_dec' => $li_grp->nature_dec,
                    'fk_id_td_event' => $li_grp->fk_id_td_event);
    }

    $req_bns = 'select * from app_campagne_bonus where fk_id_campagne in (' . implode(',', array_keys($grp_dec)) . ')';
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
    }

    if ($localisation) {
        $reqLoc = $connection->query('select fkid_wilaya, cellid from ref_wilaya_cellid');
        while ($li_loc = $reqLoc->fetch(PDO::FETCH_OBJ))
            $arr_localisation[$li_loc->cellid] = $li_loc->fk_id_wilaya;
    }
}

$reqLim = 'select id, cmp_nbr_bonus, cmp_montant_bonus, cmp_nbr_bonus_jr, cmp_montant_bonus_jr, client_nbr_bonus, client_montant_bonus,
                client_nbr_bonus_jr,client_montant_bonus_jr from app_campagne where etat = ' . CMP_ENCOURS;
$resLim = $connection->query($reqLim);
while ($li_lim = $resLim->fetch(PDO::FETCH_OBJ)) {
    $cmp_lim[$li_lim->id] = array(
        'cmp_nbr_bonus' => $li_lim->cmp_nbr_bonus,
        'cmp_montant_bonus' => $li_lim->cmp_montant_bonus,
        'cmp_nbr_bonus_jr' => $li_lim->cmp_nbr_bonus_jr,
        'cmp_montant_bonus_jr' => $li_lim->cmp_montant_bonus_jr,
        'cmp_total' => $li_lim->cmp_nbr_bonus + $li_lim->cmp_montant_bonus + $li_lim->cmp_nbr_bonus_jr + $li_lim->cmp_montant_bonus_jr,
        'client_nbr_bonus' => $li_lim->client_nbr_bonus,
        'client_montant_bonus' => $li_lim->client_montant_bonus,
        'client_nbr_bonus_jr' => $li_lim->client_nbr_bonus_jr,
        'client_montant_bonus_jr' => $li_lim->client_montant_bonus_jr,
        'client_total' => $li_lim->client_nbr_bonus + $li_lim->client_montant_bonus + $li_lim->client_nbr_bonus_jr + $li_lim->client_montant_bonus_jr
    );
}
?>