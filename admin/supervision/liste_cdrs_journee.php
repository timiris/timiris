<?php
require_once "../../fn_security.php";
check_session();
require_once "../../fn_formatter_date.php";
if(isset($_POST['jf'])){
    $arr = explode('_', $_POST['jf']);
    $ent_per = 'Jour'.$arr['1'];
}else
    exit ();
$idTable = "dataTable$ent_per";
?>
<table id = '<?php echo $idTable; ?>' class=" dataTable dispaly <?php echo $clsT; ?>" cellspacing="0" width="100%">
    <thead>
        <tr align ="left">
           <th>Fichier</th><th>Date Chargement</th><th>Nb Ligne</th><th>Nb L.Considéré</th><th>date First</th><th>Date last</th>
        </tr>
    </thead>
    <tbody>
        <?php
        require_once "../../conn/connection.php";
        $req = "select * from app_fichier_charge where dt_jour = '".$arr[1]."' and type_fichier = '".$arr[0]."' order by dt_first desc";

        $result = $connection->query($req);
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            echo "<tr>
                    <td>$ligne->fichier</td>
                    <td>".formatter_date($ligne->dt_chargement)."</td>
                    <td>".number_format($ligne->nbr_ligne, 0, '.', ' ')."</td>
                    <td>".number_format($ligne->nbr_ligne_considere, 0, '.', ' ')."</td>
                    <td>".formatter_date($ligne->dt_first)."</td>
                    <td>".formatter_date($ligne->dt_last)."</td>
            </tr>";
        }
        ?>
    </tbody>
</table>
<script>
    fnDataTable(4, "desc", "fichier", "<?php echo $idTable; ?>");
</script>