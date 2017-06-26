<?php

try {

//    require_once 'function_insert.php';
    function execute_req_table($tb, $arRq, $connection) {
        try {
            $tbAttrMSISDN = array();
            $nums = array_keys($arRq);

            $chaineMSISDN = "'" . implode("','", $nums) . "'";
            $reqVerifNum = 'SELECT numero FROM ' . $tb . ' WHERE numero in (' . $chaineMSISDN . ')';
            $result = $connection->query($reqVerifNum);
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $tbAttrMSISDN[] = $ligne->numero;
            }

            $tbNtMSISDN = array_diff($nums, $tbAttrMSISDN);
            if (count($tbNtMSISDN)) {
                foreach ($tbNtMSISDN as $num) {
                    $tReq = 'INSERT INTO ' . $tb . ' (numero, ' . implode(', ', array_keys($arRq[$num])) . ')
                            VALUES (\'' . $num . '\', ' . implode(', ', array_values($arRq[$num])) . ')';
                    $result = $connection->query($tReq);
                }
            }

// ********* update des tables data
            foreach ($tbAttrMSISDN as $num) {
                $ar = array();
                foreach ($arRq[$num] as $ch => $v)
                    $ar[] = "$ch = $ch + $v";
                $reqUpdate = "UPDATE $tb SET " . implode(', ', $ar) . " WHERE numero = '$num'";
//                echo "\r\n <br>" . $reqUpdate . ';';
                $result = $connection->query($reqUpdate);
            }
        } catch (Exception $e) {
            throw($e);
        }
    }

    function add_arrays($a, $b) {
        foreach ($b as $k => $v) {
            if (isset($a[$k]))
                $a[$k] += $v;
            else
                $a[$k] = $v;
        }
        return $a;
    }

    function fn_gen_rq($tb, $msisdn, $arr) {
        global $allRq;
        if (isset($allRq[$tb][$msisdn]))
            $allRq[$tb][$msisdn] = add_arrays($allRq[$tb][$msisdn], $arr);
        else
            $allRq[$tb][$msisdn] = $arr;
    }

    $rep_char = "/tim_arch/sauvegarde/in/Mechili_report/";
    $rep_char = "E:/depot/test/tim_arch/";
    $rep_sauv = "/tim_arch/sauvegarde/in/Mechili_sauv/";
    $rep_sauv = "E:/depot/test/tim_arch/Mechili/";
    //require_once 'config.php';
    //require_once 'fn.php';
    require_once '../connection.php';
    $crp = $connection->query("SELECT champ, h_date FROM historique_correspondance");
    $tbc = array();
    while ($lic = $crp->fetch(5)) {
        $tbc{str_replace('-', '', $lic->h_date)} = $lic->champ;
    }

    $tbFiles = scandir($rep_char);
    $new_mechili = array();
    $new_mechili['1_99'] = array('inf' => 100, 'sup' => 9900);
    $new_mechili['100_199'] = array('inf' => 10000, 'sup' => 19900);
    $new_mechili['200_299'] = array('inf' => 20000, 'sup' => 29900);
    $new_mechili['300_499'] = array('inf' => 30000, 'sup' => 49900);
    $new_mechili['500_999'] = array('inf' => 50000, 'sup' => 99900);
    $new_mechili['1000_1999'] = array('inf' => 100000, 'sup' => 199900);
    $new_mechili['2000_2999'] = array('inf' => 200000, 'sup' => 299900);
    $new_mechili['3000_4999'] = array('inf' => 300000, 'sup' => 499900);
    $new_mechili['5000_9999'] = array('inf' => 500000, 'sup' => 999900);
    $new_mechili['10000_2000000'] = array('inf' => 1000000, 'sup' => 200000000);
    unset($tbFiles[0]);
    unset($tbFiles[1]);
    foreach ($tbFiles as $fileName) {
        $nj = $tj = 0;
        $allRq = array();
        $fichier = $rep_char . $fileName;
        if (!is_file($fichier))
            continue;
        $connection->beginTransaction();
        //****************************** DEB PARTIE TRAITEMENT *********************
        $dj = $fileName;
        $dm = substr($fileName, 0, 6);
        $da = substr($fileName, 0, 4);
        $fp = fopen($fichier, 'r');
        while (!feof($fp)) {
            $li = fgets($fp, 4000);
            $tabChTot = $tabChNbr = array();
            if (strlen($li) > 14) {
                $cdr = explode("|", $li);
                $MSISDN = $cdr{0};
                $valeur = $cdr{1};
                $tj += $valeur;
                $nj++;

                if (isset($tbc[$dj])) {
                    $nmCh = $tbc[$dj];
                    $tabChTot[$nmCh] = $valeur;
                    $tabChNbr[$nmCh] = 1;
                }

                if (isset($tbc[$dm])) {
                    $nmCh = $tbc[$dm];
                    $tabChTot[$nmCh] = $valeur;
                    $tabChNbr[$nmCh] = 1;
                }
                if (isset($tbc[$da])) {
                    $nmCh = $tbc[$da];
                    $tabChTot[$nmCh] = $valeur;
                    $tabChNbr[$nmCh] = 1;
                }
                $plage = "";
                foreach ($new_mechili as $key => $value) {
                    if ($valeur <= $value['sup'] && $valeur >= $value['inf']) {
                        $plage = $key;
                        break;
                    }
                }
                if ($plage != "") {
                    fn_gen_rq("data_recharge_valeur_mechili_" . $plage, $MSISDN, $tabChTot);
                    fn_gen_rq("data_recharge_nombre_mechili_" . $plage, $MSISDN, $tabChNbr);
                }
            }
        }
        fclose($fp);
        if (count($allRq)) {
            foreach ($allRq as $tb => $arr)
                execute_req_table($tb, $arr, $connection);
            echo "La journée $fileName contient $nj rechages et le montant total est : $tj <br>\n\r";
        }
        $fichier = $rep_char . $fileName;
        $nvEmpNom = $rep_sauv . $fileName;
        $fs = rename($fichier, $nvEmpNom);
        $connection->commit();
    }
} catch (PDOException $e) {
    $connection->rollBack();
    echo "\r\n";
    echo($e->getMessage());
    echo "\r\n";
}
$connection = null;
?>