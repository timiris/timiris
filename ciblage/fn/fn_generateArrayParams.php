<?php

function generateArrayParams($tab_params) {
//    print_r($tab_params);
    $tables = array();
    foreach ($tab_params as $key => $val) {
        $tabExp = explode("_", $key);
        if (is_array($val)) {
            $val = '(' . implode(', ', $val) . ')';
        }
        if (!isset($tables["G" . $tabExp[1]]))
            $tables["G" . $tabExp[1]] = array();
        if (count($tabExp) == 2) { // assotiation Critéres
            $tables["G" . $tabExp[1]]["association"] = $val;
        } else {   //(count($tabExp) == 3 && $tab[0] == "natureType"){	// table
            $tables["G" . $tabExp[1]]["C" . $tabExp[2]][$tabExp[0]] = $val;
            if ($tabExp[0] == "idPeriodeFrom" || $tabExp[0] == "idPeriodeTo") {
                $tables["G" . $tabExp[1]]["C" . $tabExp[2]][str_replace('id', 'rel', $tabExp[0])] =
                        getDateRel($val, $tab_params['idUnitePeriodique_' . $tabExp[1] . '_' . $tabExp[2]]);
            }
        }
    }

    foreach ($tables as $Gr => $tbC) {
        if (count($tbC) == 1 && array_keys($tbC)[0] == 'association') {
            unset($tables[$Gr]);
            continue;
        }
        foreach ($tbC as $cr => $vCr) {
            if ($cr == 'association' || substr($vCr['natureType'], 0, 2) == "1_")
                continue;

            switch (trim($vCr['operateur'])) {
                case '=': if (0 == $vCr['valeurCritere']) {
                        $tables[$Gr][$cr]['estInverse'] = 1;
                        $tables[$Gr][$cr]['operateur'] = ' != ';
                    }
                    else
                        $tables[$Gr][$cr]['estInverse'] = 0;
                    break;
                case '!=': if (0 != $vCr['valeurCritere']) {
                        $tables[$Gr][$cr]['estInverse'] = 1;
                        $tables[$Gr][$cr]['operateur'] = ' = ';
                    }
                    else
                        $tables[$Gr][$cr]['estInverse'] = 0;
                    break;
                case '>=': if (0 >= $vCr['valeurCritere']) {
                        $tables[$Gr][$cr]['estInverse'] = 1;
                        $tables[$Gr][$cr]['operateur'] = ' < ';
                    }
                    else
                        $tables[$Gr][$cr]['estInverse'] = 0;
                    break;
                case '<=': if (0 <= $vCr['valeurCritere']) {
                        $tables[$Gr][$cr]['estInverse'] = 1;
                        $tables[$Gr][$cr]['operateur'] = ' > ';
                    }
                    else
                        $tables[$Gr][$cr]['estInverse'] = 0;
                    break;
                case '>': if (0 > $vCr['valeurCritere']) {
                        $tables[$Gr][$cr]['estInverse'] = 1;
                        $tables[$Gr][$cr]['operateur'] = ' <= ';
                    }
                    else
                        $tables[$Gr][$cr]['estInverse'] = 0;
                    break;
                case '<': if (0 < $vCr['valeurCritere']) {
                        $tables[$Gr][$cr]['estInverse'] = 1;
                        $tables[$Gr][$cr]['operateur'] = ' >= ';
                    }
                    else
                        $tables[$Gr][$cr]['estInverse'] = 0;
                    break;
                default: $tables[$Gr][$cr]['estInverse'] = 0;
            }
        }
    }
//    print_r($tables);

    return $tables;
}

?>