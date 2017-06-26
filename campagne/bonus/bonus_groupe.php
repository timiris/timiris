<?php
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
if (isset($_POST["idBonus"]) and !empty($_POST["idBonus"])) {
    $idBonus = $_POST["idBonus"];
} elseif (isset($idGroup))
    $idBonus = $idGroup;
if (!isset($idBonus))
    $idBonus = 1000;
?>

<div id="Bonus<?php echo $idBonus; ?>" class = "divBonus">
    <fieldset id="divGroupeBonus<?php echo $idBonus; ?>" class="divGroupeCritere subSection"  style = "border-radius:25px;">
        <legend>Bonus</legend>
        <div class="ajouterDIV" name ='ajouterDiv<?php echo $idBonus; ?>'title="Ajouter Bonus"></div>
        <div style="margin-bottom: 5px;">
            <?php
            $labelBONUS = ($idBonus == 'bnsgeneral' || $idBonus == 1000) ? 'SMS COMMUNICATION BONUS' : 'SMS COMMUNICATION BONUS GROUPE';
            ?>
            <input type="checkbox" class="ShowSMSBonus" id="idShowSMSBonus<?php echo $idBonus; ?>">
            <label for="idShowSMSBonus<?php echo $idBonus; ?>"> <?php echo $labelBONUS; ?></label><br>
            <textarea rows="2" cols="40" name='smsTeasignFr<?php echo $idBonus; ?>' class='smsComms' style="display: none;" id="idSMSBonusFr<?php echo $idBonus; ?>" placeholder ='SMS de communication en français'></textarea>
            <textarea rows="2" cols="40" name='smsTeasignAr<?php echo $idBonus; ?>' class='arabic smsComms' dir="rtl" style="display: none;" id="idSMSBonusAr<?php echo $idBonus; ?>" placeholder='رسالة نصية باللغة العربية'></textarea>
            <br><center><span align='right' name='smsTeasignAr<?php echo $idBonus; ?>Span'></span><span name='smsTeasignFr<?php echo $idBonus; ?>Span'></span></center>
            <br />
            <ul class="tags" id ="tags_<?php echo $idBonus; ?>" style="display: none;">
                <li><b>Variables :&nbsp;&nbsp;</b></li>
                <li class="tagBns" name='{$msisdn}'>Numéro</li>
                <li class="tagBns" name='{$nom}'>Nom</li>
                <li class="tagBns" name='{$solde}'>Solde</li>
                <li class="tagBns" name='{$sfidelity}'>Solde fidélité</li>
                <li class="tagBns" name='{$bns_values}'>Bonus valeur</li>
            </ul>
        </div>
        <?php
        if (isset($type) && $type == 'fidelite')
            require_once '../bonus/bonus.php';
        ?>
    </fieldset>
</div>