<?php
require_once "../../fn_security.php";
check_session();
$ent_per = 'Mois';
$fn = 'dt_mois';
$cls = " dt_mois != '' ";
$cld = " ch_date ";
$tb = 'Moi';
$clsT = 'liste_cdrs';
$clsF = '';
if(isset($_POST['periode'])){
    $ent_per = 'Jour'.$_POST['periode'];
    $fn = 'dt_jour';
    $cls = " dt_mois = '".$_POST['periode']."' ";
    $tb = 'profil';
    $cld = "";
    $clsF = 'clsFile';
}
$idTable = "dataTable$ent_per";
?>
<table id = '<?php echo $idTable; ?>' class=" dataTable dispaly <?php echo $clsT; ?>" cellspacing="0" width="100%">
    <thead>
        <tr align ="left">
            <th><?php echo $ent_per; ?></th>
            <th>MSC<br>Nb.F</th><th>MSC<br>Nb.L</th><th>MSC<br>Nb.L.C</th>
            <th>REC<br>Nb.F</th><th>REC<br>Nb.L</th><th>REC<br>Nb.L.C</th>
            <th>SMS<br>Nb.F</th><th>SMS<br>Nb.L</th><th>SMS<br>Nb.L.C</th>
            <th>MGR<br>Nb.F</th><th>MGR<br>Nb.L</th><th>MGR<br>Nb.L.C</th>
            <th>VOU<br>Nb.F</th><th>VOU<br>Nb.L</th><th>VOU<br>Nb.L.C</th>
            <th>DATA<br>Nb.F</th><th>DATA<br>Nb.L</th><th>DATA<br>Nb.L.C</th>
        </tr>
    </thead>
    <tbody>
        <?php
        require_once "../../conn/connection.php";
        $req = "select $fn as dt, 
                    count(case type_fichier when 'b' then id else null end) nbf_msc, 
                    sum(case type_fichier when 'b' then nbr_ligne else 0 end) nbl_msc,
                    sum(case type_fichier when 'b' then nbr_ligne_considere else 0 end) nbc_msc,
                    count(case type_fichier when 'rec' then id else null end) nbf_rec, 
                    sum(case type_fichier when 'rec' then nbr_ligne else 0 end) nbl_rec,
                    sum(case type_fichier when 'rec' then nbr_ligne_considere else 0 end) nbc_rec,
                    count(case type_fichier when 'sms' then id else null end) nbf_sms, 
                    sum(case type_fichier when 'sms' then nbr_ligne else 0 end) nbl_sms,
                    sum(case type_fichier when 'sms' then nbr_ligne_considere else 0 end) nbc_sms,
                    count(case type_fichier when 'mgr' then id else null end) nbf_mgr, 
                    sum(case type_fichier when 'mgr' then nbr_ligne else 0 end) nbl_mgr,
                    sum(case type_fichier when 'mgr' then nbr_ligne_considere else 0 end) nbc_mgr ,
                    count(case type_fichier when 'vou' then id else null end) nbf_vou, 
                    sum(case type_fichier when 'vou' then nbr_ligne else 0 end) nbl_vou,
                    sum(case type_fichier when 'vou' then nbr_ligne_considere else 0 end) nbc_vou ,
                    count(case type_fichier when 'data' then id else null end) nbf_data, 
                    sum(case type_fichier when 'data' then nbr_ligne else 0 end) nbl_data,
                    sum(case type_fichier when 'data' then nbr_ligne_considere else 0 end) nbc_data
                from app_fichier_charge where $cls group by dt order by dt desc";

        $result = $connection->query($req);
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            echo "<tr id = 'ligne_tc_" . $ligne->dt . "'>
                    <td><span class='$cld details'>" . $ligne->dt . "</span></td>
                    <td name = 'b_$ligne->dt' class ='$clsF'>" . $ligne->nbf_msc . "</td><td>".$ligne->nbl_msc . "</td><td>".$ligne->nbc_msc . "</td>
                    <td name = 'rec_$ligne->dt' class ='$clsF'>" . $ligne->nbf_rec . "</td><td>".$ligne->nbl_rec . "</td><td>".$ligne->nbc_rec . "</td>
                    <td name = 'sms_$ligne->dt' class ='$clsF'>" . $ligne->nbf_sms . "</td><td>".$ligne->nbl_sms . "</td><td>".$ligne->nbc_sms . "</td>
                    <td name = 'mgr_$ligne->dt' class ='$clsF'>" . $ligne->nbf_mgr . "</td><td>".$ligne->nbl_mgr . "</td><td>".$ligne->nbc_mgr . "</td>
                    <td name = 'vou_$ligne->dt' class ='$clsF'>" . $ligne->nbf_vou . "</td><td>".$ligne->nbl_vou . "</td><td>".$ligne->nbc_vou . "</td>
                    <td name = 'data_$ligne->dt' class ='$clsF'>" . $ligne->nbf_data . "</td><td>".$ligne->nbl_data . "</td><td>".$ligne->nbc_data . "</td>
            </tr>";
        }
        ?>
    </tbody>
</table>
<script>
    fnDataTable(0, "desc", "<?php echo $tb; ?>", "<?php echo $idTable; ?>");
</script>