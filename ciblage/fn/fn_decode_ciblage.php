<?php

function decode_ciblage($ciblage, $show = 0) {
    global $libNature, $libTypeDonnees, $libCompteur, $tbOperateur, $libAttSelect, $libAttChoix, $tbFormule, $libCritereAtt;
//    print_r($libCritereAtt);
    $arr_inv_op = array('!=' => '=', '=' => '!=', '<' => '>=', '>' => '<=', '<=' => '>', '>=' => '<');
//    $arr_inv_op = array('!='=> 'Egal à', '='=> 'Différent de', '<'=> 'Supérieur ou égal', '>'=> 'Inférieur ou égal', '<='=> 'Superieur de', '>='=> 'Inférieur de');
    $retCible = '';
    $uniteP = array('j' => 'Jour', 'm' => 'Mois', 'a' => 'Année');
    $tbAssoc = array('AND' => 'Appliquer tous les critères', 'OR' => 'Appliquer au moins un critère');
    $table = json_decode($ciblage, true);
    $i = 1;
    foreach ($table as $grp => $tbG) {
        $assoc = strtoupper($tbG["association"]);
        $retCible .= '<ul>';
        $retCible .= '<fieldset class="divGroupeCritere subSection"  style = "border-radius:15px; padding-left:25px;">';
        $retCible .='<legend>Groupe numéro ' . $i . ' </legend>';
        $retCible .= '<b>Association (' . $tbAssoc[$assoc] . ')</b>';
        $i++;
        unset($tbG["association"]);
        $j = 1;
        foreach ($tbG as $cr => $tbC) {
            $tbExp = explode('_', $tbC['natureType']);
            $nature = $tbExp[0];
            $type = $tbExp[1];
            $retCible .= '<li><b>Critére numéro ' . $j . '</b> : {Nature Trafic : ' . $libNature[$nature] . '}/{Type données : ' . $libTypeDonnees[$type]['libelle'] . '}</li>';
            if ($nature == 1) { // Attrb
                if (in_array($tbC['idTypeCompteur'], $libAttSelect)) {
                    if (trim($tbC['operateur']) == 'in' || trim($tbC['operateur']) == 'not in') {
                        // enlever parenthéses puis evaluer
                        $tbC['valeurCritere'] = substr($tbC['valeurCritere'], 1, strlen($tbC['valeurCritere']) - 2);
                        $valExp = explode(',', $tbC['valeurCritere']);
                        $tbListValLibelle = array();
                        foreach ($valExp as $val) {
                            // $val = substr($val, 1, strlen($val)-2);
                            $val = str_replace('\'', '', $val);
                            $val = trim($val);
                            $tbListValLibelle[] = $libAttChoix[$tbC['idTypeCompteur']][$val];
                        }
                        $tbC['valeurCritere'] = '(' . implode(', ', $tbListValLibelle) . ')';
                    }
                    else // Evaluer
                        $tbC['valeurCritere'] = $libAttChoix[$tbC['idTypeCompteur']][$tbC['valeurCritere']];
                    // $retCible .= 'Condition ciblage : '.$tbC['idTypeCompteur'].' '.$tbOperateur[trim($tbC['operateur'])].' '.$tbC['valeurCritere'].'<br>';
                }
                // else
                $retCible .= 'Condition ciblage : <font color=blue>' . $libCritereAtt[$tbC['idTypeCompteur']] . ' ' . $tbOperateur[trim($tbC['operateur'])] . ' ' . $tbC['valeurCritere'] . '</font>';
            }
            else {
                $tbC['operateur'] = (isset($tbC['estInverse']) && $tbC['estInverse'] && $show) ? $arr_inv_op[trim($tbC['operateur'])] : trim($tbC['operateur']);
                $retCible .= 'Condition ciblage : <font color=blue>' . $tbFormule[trim($tbC['idFormule'])] . ' par ' . $uniteP[$tbC['idUnitePeriodique']] . ' sur ' . $libCompteur[$type . '_' . $tbC['idTypeCompteur']] . '<br>';
                $retCible .= 'Pour la période : ' . getDateRelInv($tbC['relPeriodeFrom'], $tbC['idUnitePeriodique']) . ' Au ' . getDateRelInv($tbC['relPeriodeTo'], $tbC['idUnitePeriodique']) . ' ' .
                        $tbOperateur[$tbC['operateur']] . ' ' . $tbC['valeurCritere'] . ' ' . $libTypeDonnees[$type]['unite'][$tbC['untieValeur']] . '</font>';
            }
            $j++;
        }
        $retCible .= '</ul></fieldset>';
    }
    return $retCible;
}

?>