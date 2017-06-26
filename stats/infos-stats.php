<?php
require_once "../fn_security.php";
check_session();
$optionNature = "";
require_once '../conn/connection.php';
$clause = (isset($_POST['stats'])) ? 'client = true' : 'stat = true';
$req_infos_nature = 'SELECT *, upper(libelle) as lib FROM ref_nature WHERE ' . $clause . ' and etat = 1 order by lib';
$infos_type = $infos_compteur = $unite_compteur = array();
$req_infos_type = 'SELECT *, upper(libelle) as lib FROM ref_type_donnee WHERE etat = 1 order by fk_id_nature, lib';
$req_infos_compteur = 'SELECT *, upper(libelle) as lib FROM ref_compteurs  WHERE etat = 1 order by fk_id_type, lib';
try {
    $result_satats = $connection->query($req_infos_nature);
    while ($ligne_satats = $result_satats->fetch(PDO::FETCH_OBJ))
        $optionNature .= "<option value = '" . $ligne_satats->id . "'>" . ucfirst(strtolower($ligne_satats->libelle)) . "</option>";
    $result_type = $connection->query($req_infos_type);
    while ($ligne_type = $result_type->fetch(PDO::FETCH_OBJ)) {
        $infos_type[$ligne_type->fk_id_nature][ucfirst(strtolower($ligne_type->libelle))] = $ligne_type->id;
//        $infos_type[$ligne_type->fk_id_nature][$ligne_type->id] = ucfirst(strtolower($ligne_type->libelle));
        $unite_type[$ligne_type->id] = json_decode($ligne_type->unite);
    }
    $result_compteur = $connection->query($req_infos_compteur);
    while ($ligne_compteur = $result_compteur->fetch(PDO::FETCH_OBJ)) {
        $infos_compteur[$ligne_compteur->fk_id_type][strtolower($ligne_compteur->libelle)] = ucfirst(strtolower($ligne_compteur->code_cmpt));
//        $infos_compteur[$ligne_compteur->fk_id_type][strtolower($ligne_compteur->code_cmpt)] = ucfirst(strtolower($ligne_compteur->libelle));
    }
    ?>
    <script>
        var jsn_type = jQuery.parseJSON('<?php echo json_encode($infos_type); ?>');
        var jsn_compteur = jQuery.parseJSON('<?php echo json_encode($infos_compteur); ?>');
        console.log(jsn_compteur);
        var jsn_unite = jQuery.parseJSON('<?php echo json_encode($unite_type); ?>');
    </script>

    <?php
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>
<div id="divEnteteStatsGlobal" class ="divShadow" width="90%" >
    <table style = "width:100%;">
        <tr>
            <th>Nature trafic</th><th>Type données</th><th>Cible</th><th>Unité</th><th>Périodicité</th>
            <th rowspan = "2" style ='white-space: nowrap;'>
                <input type ="button" class="button12 blue" id = "idAffichageStatsGlobal" value="Afficher" style="width:100px;">
                <img src="img/excelNew.png" id ="icon_exp_excel" title="Exporter Excel"/>
                <form id ="frm_exp_excel" method ="POST" action="stats/stats.php">
                    <input type ="hidden" id ="idInputParms" name ="parms">
                </form>
            </th>
        </tr>
        <tr align = "center">
            <td>
                <SELECT id = "nature_stats_client" class="sel_stat">
                    <option value = "">   </option>
                    <?php echo $optionNature; ?>
                </SELECT>
            </td>
            <td>
                <SELECT id = "type_donnee_stats_client" class="sel_stat">
                    <option value = "">   </option>
                </SELECT>
            </td>
            <td>
                <SELECT multiple="multiple" id = "compteurs_stats_client" class="sel_stat">
                    <option value = "">   </option>
                </SELECT>
            </td>
            <td>
                <SELECT id = "unite_stats_client" style ="width:100px">
                </SELECT>
            </td>
            <td>
                <SELECT id = "periodicite_stats_client" style ="width:100px">
                    <option value = "j">Jours</option>
                    <option value = "m">Mois</option>
                    <option value = "a">Année</option>
                </SELECT>
            </td>
        </tr>
    </table>
</div>
<br>
<div id="stat_excel"></div>
<div id="divStatsGlobal" class ="divShadow"></div>

<script>
    $('#compteurs_stats_client').multipleSelect();
</script>