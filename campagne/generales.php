<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
if (isset($_POST['idCmp'])) {
    require_once "../conn/connection.php";
    $cmp = $connection->query('SELECT * FROM app_campagne WHERE id = ' . (int) $_POST['idCmp']);
    if ($cmp->rowCount()) {
        $liCmp = $cmp->fetch(PDO::FETCH_OBJ);
        $CMPname = $liCmp->nom;
        $CMPlancement = $liCmp->dt_lancement;
        $CMPdtFin = $liCmp->dt_fin;
        $CMPobjectif = $liCmp->objectif;
        $CMPsmsAr = $liCmp->sms_ar;
        $CMPsmsFr = $liCmp->sms_fr;
        $broadcast = $liCmp->broadcast;
    }
} else {
    $CMPname = $CMPlancement = $CMPdtFin = $CMPobjectif = $CMPsmsAr = $CMPsmsFr = '';
    $broadcast = 2;
}
$arr_br = array('Silencieuse', 'Langue abonné', 'Toutes les langues', 'Arabe seulement', 'Français seulement');
?>
<table class = "tableInfosCampagne" >
    <tr>
        <th class = "tabs" style="width:25%;">Nom de la campagne</th>
        <td colspan='2'><input type = "text" name = "nom_campagne" class ="obligatoire" size = "25" id="idNomCampagne" value ="<?= $CMPname; ?>"/></td>
    </tr>
    <tr>
        <th class = "tabs">Date Début</th>
        <td colspan='2'><input type = "text" name = "date_lancement" class = "datetimepicker" value ="<?= $CMPlancement; ?>" size = "25" placeholder="à la validation" id="idDtFromCampagne"/></td>
    </tr>
    <tr>
        <th class = "tabs">Date Fin</th>
        <td colspan='2'><input type = "text" name = "date_fin" class = "datetimepicker obligatoire" value ="<?= $CMPdtFin; ?>" size = "25" id="idDtToCampagne"/></td>
    </tr>
    <tr>
        <th class = "tabs">Objectif de la campagne</th>
        <td colspan='2'><textarea rows = "4" cols ="40" class ="obligatoire smsComms" id="idObjectifCampagne"><?= $CMPobjectif; ?></textarea></td>
    </tr>
    <tr>
        <th class = "tabs">BroadCast</th>
        <td colspan='2'>
            <select name ="broadcast" id ='idBroadCastCmp'>
                <?php
                foreach ($arr_br as $key => $lib) {
                    if ($broadcast == $key)
                        echo "<option value='$key' SELECTED>$lib</option>";
                    else
                        echo "<option value='$key'>$lib</option>";
                }
                ?>
            </select>
        </td>
    </tr>
    <tr class='teasing fr'>
        <th class = "tabs">SMS Teasing Fr</th>
        <td colspan='2'>
            <textarea rows = "4" cols ="40" name='smsTeasignFr' class ="obligatoire smsComms" id="idSmsCampagneFr" placeholder ='SMS de communication en français'><?= $CMPsmsFr; ?></textarea>
            <br><span name='smsTeasignFrSpan'></span>
        </td>
    </tr>
    <tr class='teasing ar'>
        <th class = "tabs">SMS Teasing Ar</th>
        <td colspan='2'>
            <textarea dir="rtl" rows = "4" cols ="40" name='smsTeasignAr' class ="arabic obligatoire smsComms" id="idSmsCampagneAr" placeholder='رسالة نصية باللغة العربية'><?= $CMPsmsAr; ?></textarea>
            <br><span name='smsTeasignArSpan'></span><br></td>
    </tr>
    <tr class='teasing'>
        <th class = "tabs">Liste des variables</th>
        <td colspan='2'>
            <div style='width:600px'>
                <ul class="tags">
                    <li class="tag" name='{$msisdn}'>Numéro</li>
                    <li class="tag" name='{$nom}'>Nom</li>
                    <li class="tag" name='{$solde}'>Solde</li>
                    <li class="tag" name='{$sfidelity}'>Solde fidélité</li>
                    <li class="tag" name='{$nni}'>NNI</li>
                    <li class="tag" name='{$dt_act}'>Date activation</li>
                    <li class="tag" name='{$dt_susp}'>Date suspension</li>
                    <li class="tag" name='{$dt_des}'>Date désactivation</li>
                </ul>
            </div>
        </td>
    </tr>
</table>
<fieldset class="divGroupeCritere"  style = "border-radius:15px; padding-left:25px; width: 80%;">
    <legend>Filtrage</legend>
    <form method="POST" enctype="multipart/form-data" id="fileUploadForm">
        <table class = "tableInfosCampagne" >
            <tr>
                <th class = "tabs" style="width:25%;">Liste Blanche</th>
                <td width ='105px'><div class='uploadlb'>
                        <input type ="file" accept=".csv" class ="cls_fl" name ="idFLb"></div></td>
                <td align='left'><span id='idFLb' class="alert-box"></span></td>
            </tr>
            <tr>
                <th class = "tabs">Liste Noire</th>
                <td><div class='uploadln'><input type ="file"  accept=".csv"class ="cls_fl" name ="idFLn"></div></td>
                <td align='left'><span id='idFLn' class="alert-box"></span></td>

            </tr>
        </table>
    </form>
</fieldset>
<script>$('#idBroadCastCmp').change();</script>