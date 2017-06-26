<?php

function generateReqCriteres($tbG, $assoc='') {
    global $libCorrespondance, $tbNameTable;
    $ret = array();
    foreach ($tbG as $cr => $tbC) {
        $chReq = array();
        if (substr($tbC['natureType'], 0, 2) == "1_") { // tb Attrb
            $tbC['tableName'] = $tbNameTable[$tbC['natureType']];
            if (substr($tbC['idTypeCompteur'], 0, 3) == "dt_") {
                $tbC['valeurCritere'] = str_replace('-', '', $tbC['valeurCritere']);
                switch ($tbC['operateur']) {
                    case '=' : $tbC['operateur'] = 'like1';
                        break;
                    case '>' : $tbC['valeurCritere'] .= '235959';
                        break;
                    case '>=' : $tbC['valeurCritere'] .= '000000';
                        break;
                    case '<' : $tbC['valeurCritere'] .= '000000';
                        break;
                    case '<=' : $tbC['valeurCritere'] .= '235959';
                        break;
                }
            }
            // Si le champs est balance, on doit multiplier par 100
            if ($tbC['idTypeCompteur'] == "balance")
                $tbC['valeurCritere'] *= 100;
            
            switch ($tbC['operateur']) {
                case 'like1' : $cnd = " like '" . $tbC['valeurCritere'] . "%' ";
                    break;
                case 'like2' : $cnd = " like '%" . $tbC['valeurCritere'] . "' ";
                    break;
                case 'like3' : $cnd = " like '%" . $tbC['valeurCritere'] . "%' ";
                    break;
                case 'in' : $cnd = " in " . $tbC['valeurCritere'];
                    break;
                case 'not in' : $cnd = " not in " . $tbC['valeurCritere'];
                    break;
                default : $cnd = $tbC['operateur'] . " '" . $tbC['valeurCritere'] . "'";
            }
            // Si le champs est cellid
            if ($tbC['idTypeCompteur'] == "cellid")
                $cnd = " in ( SELECT cellid FROM ref_wilaya_cellid where fkid_wilaya $cnd )";

            if (isset($ret[$tbC['tableName']]))
                $ret[$tbC['tableName']] .= "$assoc " . $tbC['idTypeCompteur'] . " $cnd ";
            else
                $ret[$tbC['tableName']] = "SELECT numero FROM " . $tbC['tableName'] . " WHERE " . $tbC['idTypeCompteur'] . " $cnd ";
        }else { // Table autre que Attrb
            $tbC['tableName'] = $tbNameTable[$tbC['natureType']] . "_" . $tbC['idTypeCompteur'];
            $tbC['idPeriodeFrom'] = getDateRelInv($tbC['relPeriodeFrom'], $tbC['idUnitePeriodique']);
            $tbC['idPeriodeTo'] = getDateRelInv($tbC['relPeriodeTo'], $tbC['idUnitePeriodique']);
            foreach ($libCorrespondance as $key => $val) {
                if ($val >= $tbC['idPeriodeFrom'] && $val <= $tbC['idPeriodeTo'] && strlen($val) == strlen($tbC['idPeriodeTo']))
                    $chReq[] = $key; //$tbC['tableName'] . '.' . $key;
            }
            if ($tbC['idPeriodeFrom'] == $tbC['idPeriodeTo']) {
                $cnd = implode(", ", $chReq);
            } else {
                $operator = $tbC['idFormule'];
                $char_imp = ', ';
                $division = '';
                if ($tbC['idFormule'] == "SUM" || $tbC['idFormule'] == "AVG") {
                    $char_imp = '+';
                    $operator = '';
                }
                if ($tbC['idFormule'] == "AVG") {
                    $division = ' /(0.1-0.1+ ' . count($chReq) . ')';
                }
                $cnd = $operator . "(" . implode($char_imp, $chReq) . ")$division ";
            }
//            if (!isset($tbC['estInverse']))
//                $tbC['estInverse'] = 0;
            if (isset($ret[$tbC['estInverse']][$tbC['tableName']])){
                if($tbC['estInverse']){
                    $ass = (strtoupper($assoc) == 'OR') ? 'AND' : 'OR';
                }else
                    $ass = $assoc;
                $ret[$tbC['estInverse']][$tbC['tableName']] .= " " . $ass . " " . $cnd . " " . $tbC['operateur'] . " " . $tbC['valeurCritere'] * $tbC['untieValeur'];
            }else
                $ret[$tbC['estInverse']][$tbC['tableName']] = "SELECT numero FROM " . $tbC['tableName'] . " WHERE " . $cnd . " " . $tbC['operateur'] . " " . $tbC['valeurCritere'] * $tbC['untieValeur'];
        }
    }
    return $ret;
}

function generateReqGroupe($assoc, $arReqCrit) {
    $rqJOIN = '';
    if (isset($arReqCrit['data_attribut']))
        $rqJOIN_att = $arReqCrit['data_attribut'];
    else {
        $rqJOIN_att = 'SELECT at.numero FROM data_attribut at WHERE ';
    }


    if ($assoc == "OR") {

        if (isset($arReqCrit[0])) {
            if (isset($arReqCrit['data_attribut']))
                $rqJOIN = ' OR ';
            $rqJOIN .= ' numero in ( ' . implode(' UNION ', $arReqCrit[0]) . ')';

            if (isset($arReqCrit[1])) {
                $rqJOIN .= ' OR ';
                if (count($arReqCrit[1]) == 1) {
                    foreach ($arReqCrit[1] as $key => $val)
                        $rqJOIN .= ' numero not in ( ' . $val . ' )';
                } else {
                    $rqJOIN_1 = ' ';
                    $idx = 1;
                    $tbFirst = '';
                    foreach ($arReqCrit[1] as $key => $val) {
                        $tbAct = "tc" . $idx;
                        $idx++;
                        if ($tbFirst == "") {
                            $tbFirst = $tbAct;
                            $rqJOIN_1 .= "( " . $val . " ) " . $tbFirst;
                        }
                        else
                            $rqJOIN_1 .= " JOIN ( " . $val . " ) " . $tbAct . " ON " . $tbAct . ".numero = " . $tbFirst . ".numero";
                    }
                    $rqEntJOIN = "SELECT $tbFirst.numero FROM ";
                    $rqJOIN .= ' numero not in (' . $rqEntJOIN . $rqJOIN_1 . ')';
                }
            }
        }
        elseif (isset($arReqCrit[1])) {
            if (isset($arReqCrit['data_attribut']))
                $rqJOIN = ' OR ';
            if (count($arReqCrit[1]) == 1) {
                foreach ($arReqCrit[1] as $key => $val)
                    $rqJOIN .= ' numero not in ( ' . $val . ' )';
            } else {
                $rqJOIN_1 = ' ';
                $idx = 1;
                foreach ($arReqCrit[1] as $key => $val) {
                    $tbAct = "tc" . $idx;
                    $idx++;
                    if ($tbFirst == "") {
                        $tbFirst = $tbAct;
                        $rqJOIN_1 .= "( " . $val . " ) " . $tbFirst;
                    }
                    else
                        $rqJOIN_1 .= " JOIN ( " . $val . " ) " . $tbAct . " ON " . $tbAct . ".numero = " . $tbFirst . ".numero";
                }
                $rqEntJOIN = "SELECT $tbFirst.numero FROM ";
                $rqJOIN .= ' numero not in (' . $rqEntJOIN . $rqJOIN_1 . ')';
            }
        }
    }
    else {
        $tbFirst = $rqJOIN = "";
        if (isset($arReqCrit[0])) {
            if (isset($arReqCrit['data_attribut']))
                $rqJOIN = ' AND ';
            if (count($arReqCrit[0]) == 1) {
                foreach ($arReqCrit[0] as $key => $val)
                    $rqJOIN .= ' numero in ( ' . $val . ' )';
            } else {
                $rqJOIN_0 = '';
                $idx = 1;
                foreach ($arReqCrit[0] as $key => $val) {
                    $tbAct = "tc" . $idx;
                    $idx++;
                    if ($tbFirst == "") {
                        $tbFirst = $tbAct;
                        $rqJOIN_0 .= "( " . $val . " ) " . $tbFirst;
                    }
                    else
                        $rqJOIN_0 .= " JOIN ( " . $val . " ) " . $tbAct . " ON " . $tbAct . ".numero = " . $tbFirst . ".numero";
                }
                $rqEntJOIN = "SELECT $tbFirst.numero FROM ";
                $rqJOIN .= ' numero in (' . $rqEntJOIN . $rqJOIN_0 . ')';
            }

            if (isset($arReqCrit[1])) {
                $rqJOIN .= ' AND numero not in ( ' . implode(' UNION ', $arReqCrit[1]) . ')';
            }
        } elseif (isset($arReqCrit[1])) {
            if (isset($arReqCrit['data_attribut']))
                $rqJOIN = ' AND ';
            $rqJOIN .= ' numero not in ( ' . implode(' UNION ', $arReqCrit[1]) . ')';
        }
    }
    $rqJOIN = $rqJOIN_att . $rqJOIN;
//    echo $rqJOIN;
    return $rqJOIN;
}

function generateRequete($tp_dmd, $tables, $associationGroupe) {
    $req = $tabRqGlobal = array();

    foreach ($tables as $grp => $tbG) {

        $assoc = strtoupper($tbG["association"]);
        unset($tbG["association"]);
        $req[$grp] = generateReqCriteres($tbG, $assoc);
        $tabRqGlobal[$grp] = generateReqGroupe($assoc, $req[$grp]);
//        $rqJOIN = $tabRqGlobal[$grp];
    }

    if (strtoupper($associationGroupe) == 'OR' || count($tabRqGlobal) == 1) {
        if ($tp_dmd == 'exp')
            $req_global = "SELECT att.points_fidelite,att.balance,att.numero,dt_active,genre,rp.libelle as rp_lib,nom,re.libelle as re_lib,dt_active_stop,dt_suspend_stop
					FROM data_attribut att
					JOIN (" . implode(" UNION ", $tabRqGlobal) . ") res on res.numero=att.numero and att.profil!=333333 and status != -1
					LEFT JOIN ref_liste_choix_attribut rp ON att.profil::varchar= rp.code and rp.attribut = 'profil'
					LEFT JOIN ref_etat_ligne_in re ON att.status = re.id";
        elseif ($tp_dmd == 'cmp')
//                $req_global = implode(" UNION ", $tabRqGlobal);
            $req_global = "SELECT att.numero FROM data_attribut att
					JOIN (" . implode(" UNION ", $tabRqGlobal) . ") res on res.numero=att.numero and att.profil!=333333 and status != -1";
        else
//                $req_global = "SELECT count(*) as nbrcible from (" . implode(" UNION ", $tabRqGlobal) . ") dual";
            $req_global = "SELECT count(*) as nbrcible FROM data_attribut att
					JOIN (" . implode(" UNION ", $tabRqGlobal) . ") res on res.numero=att.numero and att.profil!=333333 and status != -1";
    }
    else {
        $tbFirst = $req_global = "";
        //print_r($tabRqGlobal);
        foreach ($tabRqGlobal as $key => $val) {
            $tbAct = $key;
            if ($tbFirst == "") {
                $tbFirst = $tbAct;
                $req_global .= ' ( ' . $val . ' ) ' . $tbFirst;
            }
            else
                $req_global .= " JOIN  (" . $val . ") $tbAct  ON " . $tbAct . ".numero = " . $tbFirst . ".numero";
        }
        if ($tp_dmd == 'exp')
            $req_global = "SELECT att.numero,dt_active,genre,rp.libelle as rp_lib,nom,re.libelle as re_lib,dt_active_stop,dt_suspend_stop
					FROM data_attribut att
					JOIN ref_liste_choix_attribut rp ON att.profil::varchar= rp.code and rp.attribut = 'profil'
					JOIN ref_etat_ligne_in re ON att.status = re.id
					JOIN (SELECT $tbFirst.numero FROM " . $req_global . ") res on res.numero=att.numero and att.profil!=333333 and status != -1";
        elseif ($tp_dmd == 'cmp')
//                $req_global = "SELECT G1.numero FROM (" . $req_global ."JOIN data_attribut att on att.numero = )";
            $req_global = "SELECT att.numero FROM data_attribut att
				JOIN (SELECT $tbFirst.numero FROM " . $req_global . ") res on res.numero=att.numero and att.profil!=333333 and status != -1";
        else
//                $req_global = "SELECT count(*) as nbrcible FROM " . $req_global;
            $req_global = "SELECT count(*) as nbrcible FROM data_attribut att
				JOIN (SELECT $tbFirst.numero FROM " . $req_global . ") res on res.numero=att.numero and att.profil!=333333 and status != -1";
    }
//    }
//    echo $req_global;
    return $req_global;
}

?>