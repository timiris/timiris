<?php
if (!isset($rep))
    $rep = '../../';
if (!isset($_SESSION))
    session_start();
require_once $rep . "fn_security.php";
check_session();
if (!isset($_POST['idCmp']))
    exit();
try {
    $idCmp = (int) $_POST['idCmp'];
    require_once $rep . "conn/connection.php";
    require_once $rep . 'fn_formatter_date.php';
    require_once $rep . 'defs.php';
    $numero = INDICATIF . substr($_POST['numero'], -LNF_NAT);
    $arr_sms = $arr_bns = array();
    $rqSMS = "select broadcast, sms_bonus_ar, sms_ar, sms_bonus_fr, sms_fr, dt_teasing, is_lang_ar
    FROM app_campagne ac
    JOIN app_campagne_cible acc on acc.fk_id_campagne = ac.id and acc.numero = '$numero' and dt_teasing is not NULL
    WHERE id = $idCmp and broadcast != 0";
    $rqSMS = $connection->query($rqSMS);
    $strSms = '<table width = "90%"><tr><th>Date</th><th>SMS</th><th>Nature</th></tr>';
    if ($rqSMS->rowCount()) {
        $rqSMS = $rqSMS->fetch(PDO::FETCH_OBJ);
        $broadcast = $rqSMS->broadcast;
        $is_lang_ar = $rqSMS->is_lang_ar;
        if ($broadcast == 2 || $broadcast == 3 || ($broadcast == 1 and $is_lang_ar))
            $strSms .= '<tr><td style="white-space: nowrap;">' . formatter_date($rqSMS->dt_teasing) . '</td><td>' . $rqSMS->sms_ar . '</td><td>Teasing</td></tr>';
        if ($broadcast == 2 || $broadcast == 4 || ($broadcast == 1 and !$is_lang_ar))
            $strSms .= '<tr><td style="white-space: nowrap;">' . formatter_date($rqSMS->dt_teasing) . '</td><td>' . $rqSMS->sms_fr . '</td><td>Teasing</td></tr>';
    }
    else
        $strSms .= '<tr><td colspan="3">Campagne silencieuse</td></tr>';
    $rqBns = "SELECT ab.*, ab.id id_bns, acg.sms_bonus_ar, acg.sms_bonus_fr, cmpt.libelle, abd.*  
    FROM app_bonus ab
    JOIN app_bonus_details abd on ab.id = abd.id_bonus
    JOIN ref_compteurs cmpt on abd.fk_id_nature = cmpt.fk_id_type and abd.code_bonus::text = cmpt.code_cmpt
    LEFT JOIN app_campagne_groupe acg on acg.id = ab.fk_id_groupe
    WHERE ab.fk_id_campagne = $idCmp and numero = '$numero'
        order by id_bns desc";
    $rqBns = $connection->query($rqBns);
    $str_bns = '<table width = "90%"><tr><th>Date action</th><th>Date attribition</th><th>Valeur</th><th>Unité</th><th>Bonus</th></tr>';
    $old_id_bns = '';
    if (!$rqBns->rowCount())
        $str_bns = 'Aucun Bonus';
    while ($li = $rqBns->fetch(PDO::FETCH_OBJ)) {
        if (!$li->fk_id_groupe)
            $sms = ($li->is_lang_ar == 'true') ? $rqSMS->sms_ar : $rqSMS->sms_fr;
        else
            $sms = ($li->is_lang_ar == 'true') ? $li->sms_bonus_ar : $li->sms_bonus_fr;

        if ($li->dt_sms != NULL && $li->id_bns != $old_id_bns) {
            $strSms .= '<tr><td style="white-space: nowrap;">' . formatter_date($li->dt_sms) . '</td><td>' . $sms . '</td><td>Notification</td></tr>';
            $old_id_bns = $li->id_bns;
        }
        $str_bns .= '<tr><td style="white-space: nowrap;">' . formatter_date($li->dt_action) . '</td>
        <td style="white-space: nowrap;">' . formatter_date($li->dt_bns) . '</td>
              <td>' . ($li->valeur / $unitBns[$li->fk_id_nature]['div']) . ' </td><td>' . $unitBns[$li->fk_id_nature]['lib'] . '</td><td>' . $li->libelle . '</td></tr> ';
    }
    if ($strSms != '')
        $strSms .= '</table>';
    $str_bns .= '</table>';
    ?>
    <div class="sky-tabs sky-tabs-pos-left sky-tabs-anim-flip">
        <input type="radio" name="sky-tabs" checked="" id="sky-tab1" class="sky-tab-content-1">
        <label for="sky-tab1"><span><span><i class="fa fa-bolt"></i>SMS</span></span></label>
        <input type="radio" name="sky-tabs" id="sky-tab2" class="sky-tab-content-2">
        <label for="sky-tab2"><span><span><i class="fa fa-picture-o"></i>Bonus</span></span></label>
        <ul class="vo">
            <li class="vo sky-tab-content-1">
                <div class="typography" style="min-height: 100px;">
                    <ul class="uldb">
                        <fieldset class="divGroupeCritere subSection" style="border-radius:15px; padding-left:25px;">
                            <?php echo $strSms; ?>
                        </fieldset>
                    </ul>
                </div>
            </li>
            <li class="vo sky-tab-content-2">
                <div class="typography" style="min-height: 100px;">
                    <ul class="uldb">
                        <fieldset class="divGroupeCritere subSection" style="border-radius:15px; padding-left:25px;">
                            <?php echo $str_bns; ?>
                        </fieldset>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <?php
} catch (Exception $e) {
    echo $e->getMessage();
}