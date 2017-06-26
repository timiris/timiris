<?php
require_once "../../fn_security.php";
check_session();
require_once "../../fn_formatter_date.php";
if (isset($_POST['jf'])) {
    $arr = explode('_', $_POST['jf']);
    $cbp = $arr['0'];
    $type = $arr['1'];
    $jour = $arr['2'];
    $ent_per = 'Jour' . $jour;
}
else
    exit();
$idTable = "dataTableMissingCdrs$ent_per";
?>
<table id = '<?php echo $idTable; ?>' class=" dataTable dispaly <?php echo $clsT; ?>" cellspacing="0" width="100%">
    <thead>
        <tr align ="left">
            <th>Fichier manquant</th>
        </tr>
    </thead>
    <tbody>
        <?php
        require_once "../../conn/connection.php";
        $req = "select seq from app_fichier_charge where dt_jour = '$jour' and type_fichier = '$type' and cbp = $cbp order by seq";
        $result = $connection->query($req);
        if ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            //********** Journée précédente
            $dtj_jp = strtotime($jour);                        //2016-11-30
            $dtj_jp = date("Ymd", strtotime("-1 days", $dtj_jp));
            $req_jp = "select min(seq) mins, max(seq) maxs
                        from app_fichier_charge 
                        where dt_jour = '$dtj_jp' and type_fichier = '$type' and cbp = $cbp";
            $result_jp = $connection->query($req_jp);
            if ($ligne_jp = $result_jp->fetch(PDO::FETCH_OBJ)) {
                $maxSeq_jp = $ligne_jp->maxs;
                if ($ligne_jp->type_fichier == 'b')
                    $maxSeqCbp = 99999999;
                else
                    $maxSeqCbp = 999999;
                if ($ligne_jp->mins == 0) {
                    $req_max_jp = "select max(seq) maxf from app_fichier_charge 
                        where dt_jour ='$dtj_jp' and cbp = $cbp and type_fichier = '$type' and seq < " . ($maxSeqCbp / 2);
                    $result_max_jp = $connection->query($req_max_jp);
                    $ligne_max_jp = $result_max_jp->fetch(PDO::FETCH_OBJ);
                    $maxSeq_jp = $ligne_max_jp->maxf;
                }
            }
            else
                $maxSeq_jp = -1;
            //******************************
            $seq = $ligne->seq;
            if ($maxSeq_jp != -1) {
                while ($seq - $maxSeq_jp > 1) {
                    $maxSeq_jp++;
                    if ($type != 'b')
                        echo "<tr><td>{$type}{$jour}_{$cbp}_" . substr("00000" . $maxSeq_jp, -6) . ".unl</td></tr>";
                    else
                        echo "<tr><td>b" . substr("0000000" . $maxSeq_jp, -8) . ".dat</td></tr>";
                }
            }

            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                while ($ligne->seq - $seq > 1) {
                    $seq++;
                    if ($type != 'b')
                        echo "<tr><td>{$type}{$jour}_{$cbp}_" . substr("00000" . $seq, -6) . ".unl</td></tr>";
                    else
                        echo "<tr><td>b" . substr("0000000" . $seq, -8) . ".dat</td></tr>";
                }
                $seq++;
            }
        }else {
            $req = "select seq from app_fichier_charge where dt_jour < '$jour' and type_fichier = '$type' and cbp = $cbp order by seq desc limit 1";
            $result = $connection->query($req);
            if ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $seq_min = $ligne->seq + 1;
                $f_min = ($type == 'b') ? "b" . substr("0000000" . $seq_min, -8) . ".dat" : "{$type}{$jour}_{$cbp}_" . substr("00000" . $seq_min, -6) . ".unl";
            }
            else
                $f_min = ' Début ';

            $req = "select seq from app_fichier_charge where dt_jour > '$jour' and type_fichier = '$type' and cbp = $cbp order by seq asc limit 1";
            $result = $connection->query($req);
            if ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $seq_max = $ligne->seq - 1;
                $f_max = ($type == 'b') ? "b" . substr("0000000" . $seq_max, -8) . ".dat" : "{$type}{$jour}_{$cbp}_" . substr("00000" . $seq_max, -6) . ".unl";
            }
            else
                $f_max = ' Fin ';
            echo "<tr><td>Du $f_min au $f_max</td></tr>";
        }
        ?>
    </tbody>
</table>
<script>
    fnDataTable(0, "desc", "fichier", "<?php echo $idTable; ?>");
</script>