<?php

require_once "bonus/config.php";

function decode_bonus($idCmp, $type_bonus_cmp, $connection) {
    try {
        if ($type_bonus_cmp == '')
            return '<h2 class="htitre">Campagne sans bonus</h2>';
        $arrTypeBonus = array('fidelite' => 'Bonus non conditionné', 'evenement' => 'Bonus sur événement',
            'cumule_j' => 'Bonus sur cumule journée', 'cumule_m' => 'Bonus sur cumule mois');
        global $conf_bonus_pp, $conf_bonus_pp_cumule;
        echo '<h2 class="htitre">' . $arrTypeBonus[$type_bonus_cmp] . '</h2>';
        if (substr($type_bonus_cmp, 0, 6) == 'cumule')
            $arrConf = $conf_bonus_pp_cumule;
        else
            $arrConf = $conf_bonus_pp;
//        $arr_inv_op = array('!=' => '=', '=' => '!=', '<' => '>=', '>' => '<=', '<=' => '>', '>=' => '<');
//        $uniteP = array('j' => 'Jour', 'm' => 'Mois', 'a' => 'Année');
//        $tbAssoc = array('AND' => 'Appliquer tout les critéres', 'OR' => 'Appliquer au moins un critére');
// récupération des groupes :
        $req_grp = 'SELECT * FROM app_campagne_groupe WHERE fk_id_campagne = ' . $idCmp;
        $res_grp = $connection->query($req_grp);
        $grp = 0;
        $retBonus = '';
        while ($li_grp = $res_grp->fetch(PDO::FETCH_OBJ)) {
            $grp ++;
            $retBonus .= decode_groupe($li_grp, $grp, $idCmp, $connection, $arrConf);
        }


// Get bns glb
        $retBonus .= decode_bnsglobal($idCmp, $connection, $arrConf);

        return $retBonus;
    } catch (PDOException $e) {
        print_r($e);
    }
}

function decode_bnsglobal($idCmp, $connection, $arrConf) {
    $retBonus = '';
    global $libTypeBonus, $libCompteur, $libNatBonus;
    $smsBns = $connection->query("SELECT sms_bonus_ar, sms_bonus_fr FROM app_campagne WHERE id = $idCmp");
    $li_sms = $smsBns->fetch(PDO::FETCH_OBJ);
    $req_bns = "SELECT * FROM app_campagne_bonus WHERE fk_id_groupe = 0 and fk_id_campagne = $idCmp order by id";
    $res_bns = $connection->query($req_bns);
    if ($res_bns->rowCount()) {
//            $retBonus .= "<h3>Bonus Global </h3>";
        $retBonus .= '<fieldset class="divGroupeCritere subSection"  style = "border-radius:15px; padding-left:25px;">';
        $retBonus .= '<legend>Bonus Global</legend>';
        $bns = 0;
        while ($li_bns = $res_bns->fetch(PDO::FETCH_OBJ)) {
            $bns++;
            $type = $li_bns->type_bonus;
            $nature = $li_bns->nature;
            $code = $li_bns->code_bonus;
            $valeur = $li_bns->valeur;
            $ch_ref = $li_bns->ch_ref;
            $unite = $li_bns->unite;

            $retBonus .= '<li><b>Bonus numéro ' . $bns . '</b> : ' . $libTypeBonus[$type] . '</li>';

            $retBonus .= '<b>Nature : </b>' . $libNatBonus[$nature]['libelle'] . '<br>';
            $retBonus .= '<b>Valeur : </b>' . $valeur;
            if ($nature == 17) {
                $retBonus .= ' Activation du service : ' . $libCompteur[$nature . '_' . $code];
            } else {
                if ($ch_ref)
                    $retBonus .= '% de ' . str_replace('%', '', $arrConf[$natGrp][$nature][$ch_ref]);
                else
                    $retBonus .= ' ' . $libNatBonus[$nature]['unite'][$unite];
                $retBonus .= ' sur le Compteur : ' . $libCompteur[$nature . '_' . $code];
            }
        }

        if ($li_sms->sms_bonus_ar != '' || $li_sms->sms_bonus_fr != '') {
            $retBonus .= '<fieldset class="divGroupeCritere subSection"  style = "border-radius:15px; padding-left:25px;">';
            $retBonus .= '<legend>SMS Notification</legend>';
            if ($li_sms->sms_bonus_ar != '')
                $retBonus .= '<p dir="rtl">' . $li_sms->sms_bonus_ar . '</p>';
            if ($li_sms->sms_bonus_fr != '')
                $retBonus .= '<p>' . $li_sms->sms_bonus_fr . '</p>';
            $retBonus .= '</fieldset>';
        }
        $retBonus .= '</fieldset>';
    }
    return $retBonus;
}

function decode_groupe($li_grp, $grp, $idCmp, $connection, $arrConf) {

    global $libTypeBonus, $libNature, $libRefEvent, $libNatBonus, $libTypeDonnees, $libTypeDonneesEvent,
    $libCompteur, $tbOperateur, $libAttChoix, $libEventSelect, $libAttSelect;
    $retBonus = '';
    $idGrp = $li_grp->id;
    $natGrp = $li_grp->fk_id_nature;
// Get dcl of the grp
//            $retBonus .= '<h3>Groupe numéro ' . $grp . ' : Sur la nature  (' . $libNature[$natGrp] . ')</h3>';
    $retBonus .= '<fieldset class="divGroupeCritere subSection"  style = "border-radius:15px; padding-left:25px;">';
    $retBonus .= '<legend>Groupe numéro ' . $idGrp . ' : Sur la nature  (' . $libNature[$natGrp] . ')</legend>';
    $req_dcl = "SELECT * FROM app_campagne_declencheur WHERE fk_id_groupe = $idGrp";
    $res_dcl = $connection->query($req_dcl);
    $c = 0;
    while ($li_dcl = $res_dcl->fetch(PDO::FETCH_OBJ)) {
        $c++;
        $retBonus .= getDeclencheur($li_dcl, $c);
    }
// Get bns of the grp
    $retBonus .= getBonusGroupe($idCmp, $idGrp, $li_grp, $arrConf, $connection);


    $retBonus .= '</fieldset>';
//            echo '</ul>';
    return $retBonus;
}

function getBonusGroupe($idCmp, $idGrp, $li_grp, $arrConf, $connection) {
    $retBonus = '';
    $natGrp = $li_grp->fk_id_nature;
    global $libTypeBonus, $libNature, $libRefEvent, $libNatBonus, $libTypeDonnees, $libTypeDonneesEvent,
    $libCompteur, $tbOperateur, $libAttChoix, $libEventSelect, $libAttSelect;
    $req_bns = "SELECT * FROM app_campagne_bonus WHERE fk_id_groupe = $idGrp  and fk_id_campagne = $idCmp order by id";
    $res_bns = $connection->query($req_bns);
    if ($res_bns->rowCount()) {
//                $retBonus .= "<hr><h4>Bonus du groupe : </h4>";
        $retBonus .= '<fieldset class="divGroupeCritere subSection" style="border-radius:25px;">
                    <legend>Bonus du groupe</legend><blockquote>';
        $bns = 0;
        while ($li_bns = $res_bns->fetch(PDO::FETCH_OBJ)) {
            $bns++;
            $type = $li_bns->type_bonus;
            $nature = $li_bns->nature;
            $code = $li_bns->code_bonus;
            $valeur = $li_bns->valeur;
            $ch_ref = $li_bns->ch_ref;
            $unite = $li_bns->unite;

            $retBonus .= '<li><b>Bonus numéro ' . $li_bns->id . '</b> : ' . $libTypeBonus[$type] . '<br/></li>';

            $retBonus .= '<b>Nature : </b>' . $libNatBonus[$nature]['libelle'] . '<br>';
            $retBonus .= '<b>Valeur : </b>' . $valeur;
            if ($nature == 17) {
                $retBonus .= ' Activation du service : ' . $libCompteur[$nature . '_' . $code];
            } else {
                if ($ch_ref)
                    $retBonus .= '% du ' . str_replace('%', '', $arrConf[$natGrp][$nature][$ch_ref]);
                else
                    $retBonus .= ' ' . $libNatBonus[$nature]['unite'][$unite];
                $retBonus .= ' sur le Compteur : ' . $libCompteur[$nature . '_' . $code];
            }
        }
        $retBonus .= '</blockquote></fieldset>';
    }
    if ($li_grp->sms_bonus_ar != '' || $li_grp->sms_bonus_fr != '') {
        $retBonus .= '<fieldset class="divGroupeCritere subSection"  style = "border-radius:15px; padding-left:25px;">';
        $retBonus .= '<legend>SMS Notification</legend>';
        if ($li_grp->sms_bonus_ar != '')
            $retBonus .= '<p dir="rtl">' . $li_grp->sms_bonus_ar . '</p>';
        if ($li_grp->sms_bonus_fr != '')
            $retBonus .= '<p>' . $li_grp->sms_bonus_fr . '</p>';
        $retBonus .= '</fieldset>';
    }
    return $retBonus;
}

function getDeclencheur($li_dcl, $c) {
    global $libTypeBonus, $libNature, $libRefEvent, $libNatBonus, $libTypeDonnees, $libTypeDonneesEvent,
    $libCompteur, $tbOperateur, $libAttChoix, $libEventSelect, $libAttSelect;
    $retBonus = '';
    $code = strtolower($li_dcl->code_declencheur);
    $operateur = $li_dcl->operateur;
    $valeur = $li_dcl->valeur;
    $unite = $li_dcl->unite;
    $fk_id_td = $li_dcl->fk_id_td;
    $fk_id_td_event = $li_dcl->fk_id_td_event;
    if ($fk_id_td) {
//                    echo $fk_id_td;
        $tp_c = $libTypeDonnees[$fk_id_td]['libelle'];
        if (count($libTypeDonnees[$fk_id_td]['unite'])) {
            if (!isset($libTypeDonnees[$fk_id_td]['unite'][$unite])) {
                echo 'We need ' . $unite;
                print_r($libTypeDonnees[$fk_id_td]['unite']);
            }
            $unite = $libTypeDonnees[$fk_id_td]['unite'][$unite];
        }
        $libCmpt = $libCompteur[$fk_id_td . '_' . $code];
    } else {
//                    echo $fk_id_td_event;
        $tp_c = $libTypeDonneesEvent[$fk_id_td_event]['libelle'];
        if (count($libTypeDonneesEvent[$fk_id_td_event]['unite']))
            $unite = $libTypeDonneesEvent[$fk_id_td_event]['unite'][$unite];
        $libCmpt = $libRefEvent[$fk_id_td_event][$code];
    }
    $retBonus .= '<li><b>Critére numéro ' . $c . '</b> sur :  ' . $tp_c . '<br/>';
    if (!$unite)
        $unite = '';
    if ($operateur == 'in' || $operateur == 'not in') {
        $tbVal = explode('|', $valeur);
        $tbLibVal = array();
        foreach ($tbVal as $k => $val) {
            $tbLibVal[] = $libAttChoix[$code][$val];
        }
        $valeur = '(' . implode(', ', $tbLibVal) . ')';
    } else {
        if ($fk_id_td && in_array($code, $libAttSelect))
            $valeur = $libAttChoix[$code][$valeur];
        elseif ($fk_id_td_event && in_array($code, $libEventSelect))
            $valeur = $libAttChoix[$code][$valeur];
    }
    $retBonus .= "Condition sur : <b>$libCmpt</b> " . $tbOperateur[$operateur] . " $valeur $unite";
    return $retBonus;
}

?>