<?php
try {
    require_once "../../fn_security.php";
    check_session();
    $ent_per = 'Mois';
    $gb = 'dt_mois';
    $cls = " dt_mois != '' ";
    $cld = " ch_date ";
    $tb = 'Moi';
    $clsT = 'liste_cdrs';
    $clsF = '';
    if (isset($_POST['periode'])) {
        $ent_per = 'Jour' . $_POST['periode'];
        $gb = 'dt_jour';
        $cls = " dt_mois = '" . $_POST['periode'] . "' ";
        $tb = 'profil';
        $cld = "";
        $clsF = 'clsFile missing';
    }
    $idTable = "dataTableMissing$ent_per";
    ?>
    <table id = '<?php echo $idTable; ?>' class=" dataTable dispaly <?php echo $clsT; ?>" cellspacing="0" width="100%">
        <thead>
            <tr align ="left">
                <th><?php echo $ent_per; ?></th>
                <th>MSC</th>
                <th>REC cpb1</th><th>REC cpb2</th>
                <th>SMS cpb1</th><th>SMS cpb2</th>
                <th>MGR cpb1</th><th>MGR cpb2</th>
                <th>VOU cpb1</th><th>VOU cpb2</th>
                <th>DATA cpb1</th><th>DATA cpb2</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once "../../conn/connection.php";
            // initialisation
            $arr_last = array('b' => array('1' => -1), 'rec' => array('101' => -1, '102' => -1)
                , 'sms' => array('101' => -1, '102' => -1)
                , 'mgr' => array('101' => -1, '102' => -1)
                , 'vou' => array('101' => -1, '102' => -1)
                , 'data' => array('101' => -1, '102' => -1));
            if ($gb == 'dt_jour') {
                $dtj = $_POST['periode'] . '01';
                $dtj = strtotime($dtj);                        //2016-11-30
                $dtj = date("Ymd", strtotime("-1 days", $dtj));
                $req = "select min(seq) mins, max(seq) maxs, cbp, type_fichier, count(*) nbr 
                        from app_fichier_charge 
                        where dt_jour = '$dtj'
                        group by cbp, type_fichier
                        order by type_fichier, cbp desc";
                $result = $connection->query($req);
                while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                    $maxSeq = $ligne->maxs;
                    if ($ligne->type_fichier == 'b')
                        $maxSeqCbp = 99999999;
                    else
                        $maxSeqCbp = 999999;

                    if ($ligne->mins == 0) {
                        $req_max = "select max(seq) maxf from app_fichier_charge 
                        where dt_jour ='$dtj' and cbp = " . $ligne->cbp . " and type_fichier = '" . $ligne->type_fichier . "' and seq < " . ($maxSeqCbp / 2);
                        $result_max = $connection->query($req_max);
                        $ligne_max = $result_max->fetch(PDO::FETCH_OBJ);
                        $maxSeq = $ligne_max->maxf;
                    }
                    $arr_last[$ligne->type_fichier][$ligne->cbp] = array($maxSeq, $dtj);
                }
            }
            // Fin

            $req = "select min(fichier) minf, max(fichier) maxf, $gb dt, cbp, type_fichier, count(*) nbr 
            from app_fichier_charge 
            where $cls 
            group by $gb, cbp, type_fichier
            order by $gb, type_fichier, cbp";
            $result = $connection->query($req);
            $arr = array();
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $li_dt = $ligne->dt;
                if (!isset($arr[$li_dt]))
                    $arr[$li_dt] = array('b' => array('1' => 'missing'), 'rec' => array('101' => 'missing', '102' => 'missing')
                        , 'sms' => array('101' => 'missing', '102' => 'missing')
                        , 'mgr' => array('101' => 'missing', '102' => 'missing')
                        , 'vou' => array('101' => 'missing', '102' => 'missing')
                        , 'data' => array('101' => 'missing', '102' => 'missing')
                    );

                if ($ligne->type_fichier == 'b') {
                    $minSeq = (int) str_replace('.dat', '', str_replace('b', '', $ligne->minf));
                    $maxSeq = (int) str_replace('.dat', '', str_replace('b', '', $ligne->maxf));
                    $maxSeqCbp = 99999999;
                } else {
                    $inf_file = explode('_', $ligne->minf);
                    if (count($inf_file == 3)) {
                        $minSeq = (int) str_replace('.unl', '', $inf_file[2]);
                    } else {
                        $minSeq = 0;
                    }


                    $inf_file = explode('_', $ligne->maxf);
                    if (count($inf_file == 3)) {
                        $maxSeq = (int) str_replace('.unl', '', $inf_file[2]);
                    } else {
                        $maxSeq = 0;
                    }
                    $maxSeqCbp = 999999;
                }

                if ($minSeq == 0) {
                    $dtj = substr(str_replace($ligne->type_fichier, '', $ligne->minf), 0, 8);
                    $req_min = "select min(seq) minf from app_fichier_charge 
                        where dt_jour ='$dtj' and cbp = " . $ligne->cbp . " and type_fichier = '" . $ligne->type_fichier . "' and seq > " . ($maxSeqCbp / 2);
                    $result_min = $connection->query($req_min);
                    $ligne_min = $result_min->fetch(PDO::FETCH_OBJ);
                    $minSeq = $ligne_min->minf;
                }
                if ($maxSeq == $maxSeqCbp) {
                    $dtj = substr(str_replace($ligne->type_fichier, '', $ligne->maxf), 0, 8);
                    $req_max = "select max(seq) maxf from app_fichier_charge 
                        where dt_jour ='$dtj' and cbp = " . $ligne->cbp . " and type_fichier = '" . $ligne->type_fichier . "' and seq < " . ($maxSeqCbp / 2);
                    $result_max = $connection->query($req_max);
                    $ligne_max = $result_max->fetch(PDO::FETCH_OBJ);
                    $maxSeq = $ligne_max->maxf;
                }

                $nbrFiles = $maxSeq - $minSeq + 1;
                if ($nbrFiles < 0)
                    $nbrFiles += $maxSeqCbp + 1;
                // Comparaison avec la date précedente
                $dif = 0;
                if (is_array($arr_last[$ligne->type_fichier][$ligne->cbp])) {
                    if ($gb == 'dt_jour') {
                        $df_date = date_diff(date_create($li_dt), date_create($arr_last[$ligne->type_fichier][$ligne->cbp][1]));
                        $nb_date = $df_date->days;
                    } else {
                        $df_date = date_diff(date_create($li_dt . '28'), date_create($arr_last[$ligne->type_fichier][$ligne->cbp][1] . '28'));
                        $nb_date = $df_date->m;
                    }
                    if ($nb_date == 1) {
                        $dif = $minSeq - $arr_last[$ligne->type_fichier][$ligne->cbp][0] - 1;
                        if ($dif < 0)
                            $dif += $maxSeqCbp + 1;
                    }
                    else
                        $arr_last[$ligne->type_fichier][$ligne->cbp] = -1;
                }
                $arr_last[$ligne->type_fichier][$ligne->cbp] = array($maxSeq, $li_dt);
                $arr[$li_dt][$ligne->type_fichier][$ligne->cbp] = $nbrFiles - $ligne->nbr + $dif;
            }
            foreach ($arr as $dt => $arr_dt) {
                $cMSC = ($arr_dt['b']['1']) ? "class ='$clsF' style='color:red'" : "";
                $cR1 = ($arr_dt['rec']['101']) ? "class ='$clsF' style='color:red'" : "";
                $cR2 = ($arr_dt['rec']['102']) ? "class ='$clsF' style='color:red'" : "";
                $cS1 = ($arr_dt['sms']['101']) ? "class ='$clsF' style='color:red'" : "";
                $cS2 = ($arr_dt['sms']['102']) ? "class ='$clsF' style='color:red'" : "";
                $cM1 = ($arr_dt['mgr']['101']) ? "class ='$clsF' style='color:red'" : "";
                $cM2 = ($arr_dt['mgr']['102']) ? "class ='$clsF' style='color:red'" : "";
                $cV1 = ($arr_dt['vou']['101']) ? "class ='$clsF' style='color:red'" : "";
                $cV2 = ($arr_dt['vou']['102']) ? "class ='$clsF' style='color:red'" : "";
                $cD1 = ($arr_dt['data']['101']) ? "class ='$clsF' style='color:red'" : "";
                $cD2 = ($arr_dt['data']['102']) ? "class ='$clsF' style='color:red'" : "";
                echo "<tr id = 'ligne_tc_" . $dt . "'>
                    <td><span class='$cld details missing'>" . $dt . "</span></td>
                    <td name = '1_b_$dt' $cMSC>" . $arr_dt['b']['1'] . "</td>
                    <td name = '101_rec_$dt' $cR1>" . $arr_dt['rec']['101'] . "</td><td name = '102_rec_$dt' $cR2>" . $arr_dt['rec']['102'] . "</td>
                    <td name = '101_sms_$dt' $cS1>" . $arr_dt['sms']['101'] . "</td><td name = '102_sms_$dt' $cS2>" . $arr_dt['sms']['102'] . "</td>
                    <td name = '101_mgr_$dt' $cM1>" . $arr_dt['mgr']['101'] . "</td><td name = '102_mgr_$dt' $cM2>" . $arr_dt['mgr']['102'] . "</td>
                    <td name = '101_vou_$dt' $cV1>" . $arr_dt['vou']['101'] . "</td><td name = '102_vou_$dt' $cV2>" . $arr_dt['vou']['102'] . "</td>
                    <td name = '101_data_$dt' $cD1>" . $arr_dt['data']['101'] . "</td><td name = '102_data_$dt' $cD2>" . $arr_dt['data']['102'] . "</td>
            </tr>";
            }
            ?>
        </tbody>
    </table>
    <script>
        fnDataTable(0, "desc", "<?php echo $tb; ?>", "<?php echo $idTable; ?>");
    </script>
    <?php
} catch (Exception $e) {
    echo $e->getMessage();
}