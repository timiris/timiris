<?php
if (!isset($defDeclencheur))
    $defDeclencheur = '';
$sel1 = $sel2 = $sel3 = $sel4 = $sel5 = '';
switch ($defDeclencheur) {
    case "":$sel1 = 'SELECTED';
        break;
    case "fidelite":$sel2 = 'SELECTED';
        break;
    case "evenement":$sel3 = 'SELECTED';
        break;
    case "cumule_j":$sel4 = 'SELECTED';
        break;
    case "cumule_m":$sel5 = 'SELECTED';
        break;
}
?>
<div id="div_type_declencheur">
    Condition d'attribution de bonus : 
    <select id='type_declencheur'>
        <option value ="" <?= $sel1; ?>>Campagne Teasing (Sans bonus)</option>
        <option value ="fidelite" <?= $sel2; ?>>Bonus non conditionné</option>
        <option value ="evenement" <?= $sel3; ?>>Bonus sur événement</option>
        <option value ="cumule_j" <?= $sel4; ?>>Bonus sur cumul journée</option>
        <option value ="cumule_m" <?= $sel5; ?>>Bonus sur cumul mois</option>
    </select>
</div>
<div id="div_detail_declencheur">
    <?php
    if(isset($arrGrp)){
        drawBonus($defDeclencheur, $arrGrp, $arrBns, $connection, $sms_bonus_ar, $sms_bonus_fr);
    }
    ?>
</div>