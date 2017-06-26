<?php
$optionNature = '';
if ($cmpEtat == CMP_ENCOURS) {   // Calcule ROI
    $rqroi = $connection->query('select sum(valorisation) v_bonus from app_bonus_details abd JOIN app_bonus ab on ab.id = abd.id_bonus and ab.fk_id_campagne = ' . $idCmp);
    if ($rqroi->rowCount())
        $v_bonus = $rqroi->fetch(PDO::FETCH_OBJ)->v_bonus;
    else
        $v_bonus = 0;

    $rqroi = $connection->query('select cible, gc FROM app_campagne_kpi WHERE fk_id_campagne =' . $idCmp . " and tbname ='data_consommation_total'");
    $rqroi = $rqroi->fetch(PDO::FETCH_OBJ);
//    if($nbrGc){
//        $consGc = $rqroi->gc / $nbrGc;
//    }else
//        $consGc = 0;
    $consCible = ($rqroi->cible / $nbrCible - (($nbrGc) ? $rqroi->gc / $nbrGc : 0)) * $nbrCible / 100;

    $roi = $consCible - $v_bonus;
    $fcolor = ($roi > 0) ? 'green' : 'red';
}
$res_nature = $connection->query('SELECT *, upper(libelle) as lib FROM ref_nature WHERE id not in(1, 11, 13, 14) order by lib');
while ($ligne_satats = $res_nature->fetch(PDO::FETCH_OBJ))
    $optionNature .= "<option value = '" . $ligne_satats->id . "'>" . ucfirst(strtolower($ligne_satats->libelle)) . "</option>";
?>
<p ><center class='alert-box notice'>Le ROI de la campagne est :<font color='<?php echo $fcolor;?>'> <?php echo number_format($roi, 2, '.', ' '); ?> UM</font></center></p>
<div id="divEnteteStatsGlobal" class ="divShadow" width="90%" >
    <table style = "width:100%;">
        <tr style='background-color: #ddd;'>
            <th>Nature trafic</th><th >Type données</th><th>Unité</th>
            <th rowspan = "2" style ='white-space: nowrap;'>
                <input type ="button" class="button12 blue" id = "idAffichageKPI" value="Afficher KPI" style="width:100px;">
                <input id = 'idCmpHidden' type="hidden" value ="<?php echo $idCmp; ?>"/>
            </th>
        </tr>
        <tr align = "center"  style='background-color: #ddd;'>
            <td>
                <SELECT id = "nature_kpi" class="sel_stat">
                    <option value = "">   </option>
                    <?php echo $optionNature; ?>
                </SELECT>
            </td>
            <td>
                <SELECT id = "type_kpi" class="sel_stat" style ="width:300px">
                    <option value = "">   </option>
                </SELECT>
            </td>            
            <td>
                <SELECT id = "unite_kpi" class="sel_stat">
                    <option value = "">   </option>
                </SELECT>
            </td>
        </tr>
    </table>
</div>
<br>
<div id="divStatsGlobal" class ="divShadow"></div>