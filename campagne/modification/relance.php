<?php
if (!isset($_SESSION))
    session_start();
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
$arr_br = array(1 => 'Langue abonné', 2 => 'Toutes les langues', 3 => 'Arabe seulement', 4 => 'Français seulement');
if (isset($_POST['idCmp'])) {
    require_once $rep . "defs.php";
    require_once $rep . "conn/connection.php";
    $idCmp = (int) $_POST['idCmp'];
    $cmp = $connection->query('SELECT * FROM app_campagne WHERE etat = ' . CMP_ENCOURS . ' AND id = ' . $idCmp);
    if ($cmp->rowCount()) {
        $cmp = $cmp->fetch(PDO::FETCH_OBJ);
        $broadcast = (!$cmp->broadcast) ? 2 : $cmp->broadcast;
        $CMPsmsAr = $cmp->sms_ar;
        $CMPsmsFr = $cmp->sms_fr;
        $disAr = ($broadcast == 4)?' display:none; ':'';
        $disFr = ($broadcast == 3)?' display:none; ':'';
        ?>
        <table class = "tableInfosCampagne" >
            <tr>
                <th class = "tabs">Date relance</th>
                <td colspan='2'>
                    <input type="text" class="datetimepicker relance" name="dt_relance" value=""/>
                </td>
            </tr>
            <tr>
                <th class = "tabs">BroadCast</th>
                <td colspan='2'>
                    <select name ="broadcast" class="relance" id ='idBroadCastCmp'>
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
            <tr class='teasing fr' style="<?= $disFr; ?>">
                <th class = "tabs">SMS Teasing Fr</th>
                <td colspan='2'>
                    <textarea rows = "4" cols ="40" name='smsTeasignFr' class ="obligatoire smsComms relance" id="idSmsCampagneFr" placeholder ='SMS de communication en français'><?= $CMPsmsFr; ?></textarea>
                    <br><span name='smsTeasignFrSpan'></span>
                </td>
            </tr>
            <tr class='teasing ar' style="<?= $disAr; ?>">
                <th class = "tabs">SMS Teasing Ar</th>
                <td colspan='2'>
                    <textarea dir="rtl" rows = "4" cols ="40" name='smsTeasignAr' class ="arabic obligatoire smsComms relance" id="idSmsCampagneAr" placeholder='رسالة نصية باللغة العربية'><?= $CMPsmsAr; ?></textarea>
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
        <?php
    }
}