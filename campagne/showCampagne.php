<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
require_once "../defs.php";
require_once "../fn_formatter_date.php";
require_once "../conn/connection.php";
require_once "../lib/tbLibelle.php";
require_once "../ciblage/fn/fn_decode_ciblage.php";
require_once "fn/fn_decode_bonus.php";
require_once "../ciblage/fn/fn_getDateRel.php";
$tbRetour['exec'] = 0;
$tbRetour['message'] = '';
if (!isset($_POST['idCmp']) || empty($_POST['idCmp']))
    exit();
$idCmp = $_POST['idCmp'];

// ORDER BY type ASC, poids DESC
try {
    $arr_br = array('Silencieuse', 'Langue abonné', 'Toutes les langues', 'Arabe seulement', 'Français seulement');
    $req = 'SELECT * FROM app_campagne WHERE id = ' . $idCmp;
    $result = $connection->query($req);
    if ($result->rowCount()) {
        $ligne = $result->fetch(PDO::FETCH_OBJ);
        $createur = $ligne->createur;
        $broadcast = $arr_br[$ligne->broadcast];
        $cmpSMSAr = $ligne->sms_ar;
        $cmpSMSFr = $ligne->sms_fr;
        $dtDebut = (trim($ligne->dt_lancement) != '') ? $ligne->dt_lancement : 'à la validation';
        $dtLancement = $ligne->dt_lancement_reelle;
        $dtFin = $ligne->dt_fin;
        $dtArret = $ligne->dt_fin_relle;
        $cmpName = $ligne->nom;
        $cmpObj = $ligne->objectif;
        $idCible = $ligne->id_cible;
        $cmpEtat = $ligne->etat;
        $typeBonus = $ligne->type_bonus;
        $chez = $ligne->chez_profil;
        $hasWl = $ligne->has_wl;
        $hasBl = $ligne->has_bl;
        $nbrCible = $ligne->nbr_cible;
        $nbrGc = $ligne->nbr_gc;
        $roi = $ligne->roi;
        $nbrTeasing = $ligne->nbr_teasing;
        $cmp_nbr_bonus = (int) $ligne->cmp_nbr_bonus;
        $cmp_montant_bonus = (int) $ligne->cmp_montant_bonus;
        $cmp_nbr_bonus_jr = (int) $ligne->cmp_nbr_bonus_jr;
        $cmp_montant_bonus_jr = (int) $ligne->cmp_montant_bonus_jr;
        $client_nbr_bonus = (int) $ligne->client_nbr_bonus;
        $client_montant_bonus = (int) $ligne->client_montant_bonus;
        $client_nbr_bonus_jr = (int) $ligne->client_nbr_bonus_jr;
        $client_montant_bonus_jr = (int) $ligne->client_montant_bonus_jr;

        $inptCmp = $inptCbl = $assocG = '';
        if ((($cmpEtat == CMP_EDTION || $cmpEtat == CMP_REJETEE) && $createur == $_SESSION["user"]["id"]) || ($cmpEtat == CMP_SOUMISE && $_SESSION["user"]["profil"] == $chez))
            $inptCmp .= '<p><input class="button12 green valider_campagne" style = "width:80px ;font-size : 70%; font-weight:bold;"  type = "button" value = "Valider" name = "valider' . $idCmp . '"></p>';
        if ($cmpEtat == CMP_SOUMISE && $_SESSION["user"]["profil"] == $chez)
            $inptCmp .= '<p><input class="button12 red gererCampagne" style = "width:80px ;font-size : 70%; font-weight:bold;"  type = "button" value = "Rejetter" name = "rejeter_' . $idCmp . '"></p>';
// Gestion des droits
        if (($cmpEtat == CMP_EDTION || $cmpEtat == CMP_REJETEE) && ($createur == $_SESSION["user"]["id"] || $_SESSION["user"]["profil"] == PROFIL_ADMIN)) {
            $inptCmp .= '<p><input class="button12 black supprimer_campagne" style = "width:80px; font-size : 70%; font-weight:bold;"  type = "button" value = "Supprimer"  name = "supprimer' . $idCmp . '"></p>';
            $inptCmp .= '<p><input class="button12 grey" id = "btnModifCmp" style = "width:80px ;font-size : 70%; font-weight:bold;"  type = "button" value = "Modifier" name = "' . $idCmp . '"></p>';
        }
        if (($cmpEtat == CMP_ENCOURS || $cmpEtat == CMP_SUSPENDUE) && ($createur == $_SESSION["user"]["id"] || $_SESSION["user"]["profil"] == PROFIL_ADMIN))
            $inptCmp .= '<p><br><input class="button12 red gererCampagne" style = "width:80px; font-size : 70%; font-weight:bold;"  type = "button" value = "Arrêter"  name = "arreter_' . $idCmp . '"></p>';
        if (($cmpEtat == CMP_ENCOURS) && ($createur == $_SESSION["user"]["id"] || $_SESSION["user"]["profil"] == PROFIL_ADMIN)) {
            $inptCmp .= '<p><input class="button12 black gererCampagne" style = "width:80px; font-size : 70%; font-weight:bold;"  type = "button" value = "Pause"  name = "pause_' . $idCmp . '"></p>';
            $inptCmp .= '<p><input class="button12 black relanceCampagne" style = "width:80px; font-size : 70%; font-weight:bold;"  type = "button" value = "Relance"  name = "' . $idCmp . '"></p>';
        }
        if (($cmpEtat == CMP_SUSPENDUE) && ($createur == $_SESSION["user"]["id"] || $_SESSION["user"]["profil"] == PROFIL_ADMIN))
            $inptCmp .= '<p><input class="button12 black gererCampagne" style = "width:80px; font-size : 70%; font-weight:bold;"  type = "button" value = "Activer"  name = "activer_' . $idCmp . '"></p>';

        $inptBl = (!$hasBl) ? '' : '<input class="button12 black export_bl" id="btnBl" style = "width:90px ;font-size : 90%; font-weight:bold;"  type = "button" value = "Liste noire" name = "' . $idCmp . '">';
        $inptWl = (!$hasWl) ? '' : '<input class="button12 white export_wl" id="btnWl" style = "width:90px ;font-size : 90%; font-weight:bold;"  type = "button" value = "Liste blanche" name = "' . $idCmp . '">';
        $inptGT = ($cmpEtat < CMP_ENCOURS) ? '' : '<input class="button12 grey export_gt" id="btnGt" style = "width:110px ;font-size : 90%; font-weight:bold;"  type = "button" value = "Groupe Contrôle" name = "' . $idCmp . '">';
        $inptEB = ($cmpEtat < CMP_ENCOURS) ? '' : '<input class="button12 grey export_gt" id="btnBns" style = "width:100px ;font-size : 90%; font-weight:bold;"  type = "button" value = "Liste Bonus" name = "' . $idCmp . '">';
        if ($idCible) {
            if ($cmpEtat < CMP_ENCOURS) {
                $cls = 'executer_cible';
                $action = 'exporter_cible';
                $idSend = $idCible;
            } else {
                $cls = 'executer_cible_sv';
                $action = 'exporter_cible_cmp';
                $idSend = $idCmp;
            }
            $inptCbl = '<form method = "POST" action = "ciblage/' . $action . '.php"  target="_blank" style="margin-top:25px;">
                        <input class="button12 black ' . $cls . '" style = "width:80px ;font-size : 70%; font-weight:bold;"  type = "button" value = "Executer" name = "executer' . $idSend . '">
                        <input type = "hidden" name = "idCible" value = "' . $idSend . '">
                        <input class="button12 black" style = "width:80px; font-size : 70%; font-weight:bold;" type = "submit" value = "Exporter" name = "exporter' . $idCible . '">
                        </form>';
            $req = 'SELECT * FROM app_cibles WHERE id = ' . $idCible;
            $result = $connection->query($req);
            if ($result->rowCount()) {
                $ligne = $result->fetch(PDO::FETCH_OBJ);
                $association_group = $ligne->association_group;
                $cible = $ligne->cible;
                $assocG = 'Appliquer ' . $tbAssocGr[strtoupper($association_group)];
            } else {
                exit();
            }
            $dec_cible = decode_ciblage($cible, 1);
        } else
            $dec_cible = "<br><br><span class='alert-box success'>Tous le parc (actif + suspended)</span>";
        ?>
        <td colspan = "7" style="margin:0;padding:0;">
            <div class="sky-tabs sky-tabs-pos-left sky-tabs-anim-flip">
                <input type="radio" name="sky-tabs" checked="" id="sky-tab1" class="sky-tab-content-1">
                <label for="sky-tab1"><span><span><i class="fa fa-bolt"></i>Générales</span></span></label>
                <input type="radio" name="sky-tabs" id="sky-tab2" class="sky-tab-content-2">
                <label for="sky-tab2"><span><span><i class="fa fa-picture-o"></i>Ciblage</span></span></label>
                <input type="radio" name="sky-tabs" id="sky-tab3" class="sky-tab-content-3">
                <label for="sky-tab3"><span><span><i class="fa fa-cogs"></i>Bonus</span></span></label>
                <input type="radio" name="sky-tabs" id="sky-tab4" class="sky-tab-content-4">
                <label for="sky-tab4"><span><span><i class="fa fa-cogs"></i>Budget</span></span></label>
                <input type="radio" name="sky-tabs" id="sky-tab5" class="sky-tab-content-5">
                <label for="sky-tab5"><span><span><i class="fa fa-cogs"></i>WorkFlow</span></span></label>
                <input type="radio" name="sky-tabs" id="sky-tab6" class="sky-tab-content-6">
                <label for="sky-tab6"><span><span><i class="fa fa-cogs"></i>ROI & KPI</span></span></label>
                <ul class="vo">
                    <li class="vo sky-tab-content-1">
                        <div class="typography" style="min-height: 200px;">
                            <h2 class="htitre"><?php echo $cmpName; ?></h2>
                            <fieldset class="divGroupeCritere subSection"  style = "border-radius:15px; padding-left:25px;">
                                <legend>Informations signalétique</legend>
                                <table align = "center" width=95% style="font-size: 14px;">
                                    <tr><td>Date début </td><td> <?php echo $dtDebut; ?></td></tr>
                                    <tr><td style ='white-space: nowrap;'>Date lancement </td><td> <?php echo $dtLancement; ?></td></tr>
                                    <tr><td>Date fin </td><td> <?php echo $dtFin; ?></td></tr>
                                    <tr><td>Date arrêt </td><td> <?php echo $dtArret; ?></td></tr>
                                    <tr><td>Objectif </td><td> <?php echo $cmpObj; ?></td></tr>
                                    <tr><td>BroadCast </td><td> <?php echo $broadcast; ?></td></tr>
                                    <tr><td>SMS Arabe</td><td dir='rtl'> <?php echo $cmpSMSAr; ?></td></tr>
                                    <tr><td>SMS Français</td><td> <?php echo $cmpSMSFr; ?></td></tr>
                                </table>
                            </fieldset>
                            <?php
                            if ($cmpEtat >= CMP_ENCOURS) {
                                $reqS = 'select count(distinct numero) nbr_msisdn, count(distinct acb.id) nbr_bonus, sum(details.valorisation) val        
                                        from app_bonus acb
                                        join app_bonus_details details on acb.id = details.id_bonus
                                        where fk_id_campagne = ' . $idCmp;
                                $ress = $connection->query($reqS);
                                $lis = $ress->fetch(PDO::FETCH_OBJ);
                                $nbrMSISDN = $lis->nbr_msisdn;
                                $nbrBONUS = $lis->nbr_bonus;
                                $valorisation = $lis->val;
                                $pt = ($nbrCible) ? number_format(100 * $nbrTeasing / $nbrCible, 1, '.', ' ') . ' %' : '!?';
                                $pb = ($nbrCible) ? number_format(100 * $nbrMSISDN / $nbrCible, 1, '.', ' ') . ' %' : '!?';
                                ?>
                                <fieldset class="divGroupeCritere subSection"  style = "border-radius:15px; padding-left:25px;">
                                    <legend>Campagne en chiffre</legend>
                                    <table align = "center" width=75% style="font-size: 14px;">
                                        <tr><td>Nbr de la cible </td><td> <?php echo number_format($nbrCible, 0, '.', ' '); ?></td></tr>
                                        <tr><td>Nbr du groupe contrôle </td><td> <?php echo number_format($nbrGc, 0, '.', ' '); ?></td></tr>
                                        <tr><td>Nbr Client sensibilisé </td><td> <?php echo number_format($nbrTeasing, 0, '.', ' ') . ' Clients (' . $pt . ' de la cible)'; ?></td></tr>
                                        <tr><td>Nbr Bonus attribué</td><td> <?php echo number_format($nbrBONUS, 0, '.', ' '); ?></td></tr>
                                        <tr><td>Nbr de bénéficiaire </td><td> <?php echo number_format($nbrMSISDN, 0, '.', ' ') . ' Numéros (' . $pb . ' de la cible)'; ?></td></tr>
                                        <tr><td>Bonus valeurs </td><td> <?php echo number_format($valorisation, 0, '.', ' '); ?> UM</td></tr>
                                    </table>
                                </fieldset>
                                <?php
                            }
                            ?>
                            <br>
                            <form method="post" action="ciblage/exporter_cible_sv.php" id="formSendExport">
                                <input type ="hidden" name="idCible" id="hIdCible">
                                <input type ="hidden" name="wl" id="hwl">
                            </form>
                            <?php echo $inptWl . ' ' . $inptBl . ' ' . $inptGT . ' ' . $inptEB; ?>
                        </div>
                    </li>

                    <li class="vo sky-tab-content-2">
                        <div class="typography" style="min-height: 200px;">
                            <h2 class="htitre">Ciblage de la campagne</h2><br>
                            <?php echo $assocG; ?>
                            <ul>
                                <?php echo $dec_cible; ?>
                            </ul>
                            <?php echo $inptCbl; ?>
                        </div>
                    </li>
                    <li class="vo sky-tab-content-3">
                        <div class="typography" style="min-height: 200px;">
                            <ul class="uldb"><?php echo decode_bonus($idCmp, $typeBonus, $connection) ?>
                            </ul>
                        </div>
                    </li>
                    <li class="vo sky-tab-content-4">
                        <div class="typography" style="min-height: 200px;">
                            <ul class="uldb">
                                <fieldset class = "divGroupeCritere subSection" style = "border-radius:15px; padding-left:25px;">
                                    <legend>Contrôle budgetaire de la campagne</legend >
                                    <table align = "center" width=75% style="font-size: 14px;">
                                        <tr><td align="center" colspan="4"><h2 class="htitre">Seuil budgétaire de la campagne</h2></td></tr>
                                        <tr><td colspan="4"><h4 style="display:inline">Dans la Campagne</h4></td></tr>
                                        <tr>
                                            <td>Nombre de bonus</td><td><?php echo $cmp_nbr_bonus; ?></td>
                                            <td>Montant de bonus</td><td><?php echo $cmp_montant_bonus; ?></td>
                                        </tr>
                                        <tr><td colspan="4"><h4 style="display:inline">Par jour</h4></td></tr>
                                        <tr>
                                            <td>Nombre de bonus</td><td><?php echo $cmp_nbr_bonus_jr; ?></td>
                                            <td>Montant de bonus</td><td><?php echo $cmp_montant_bonus_jr; ?></td>
                                        </tr>

                                        <tr><td align="center" colspan="4"><h2 class="htitre">Seuil budgétaire par abonné</h2></td></tr>
                                        <tr><td colspan="4"><h4 style="display:inline">Dans la Campagne</h4></td></tr>
                                        <tr>
                                            <td>Nombre de bonus</td><td><?php echo $client_nbr_bonus; ?></td>
                                            <td>Montant de bonus</td><td><?php echo $client_montant_bonus; ?></td>
                                        </tr>
                                        <tr><td colspan="4"><h4 style="display:inline">Par jour</h4></td></tr>
                                        <tr>
                                            <td>Nombre de bonus</td><td><?php echo $client_nbr_bonus_jr; ?></td>
                                            <td>Montant de bonus</td><td><?php echo $client_montant_bonus_jr; ?></td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </ul>
                        </div>
                    </li>
                    <li class="vo sky-tab-content-5">
                        <div class="typography" style="min-height: 200px;">
                            <ul class="uldb">
                                <h2 class="htitre">WorkFlow de la campagne</h2>
                                <br><br>
                                <?php
                                $reqWF = 'SELECT lib_action, dt_action, u.nom unom, u.prenom uprenom, profil.nom pnom, commentaire FROM app_campagne_wf wf
                                        JOIN sys_profil profil on profil.id = wf.id_profil
                                        JOIN sys_users u on u.id = wf.id_user
                                        JOIN ref_etat_campagne retat on retat.id = wf.new_status
                                        WHERE fk_id_campagne = ' . $idCmp . ' order by wf.dt_action';
                                $resWF = $connection->query($reqWF);
                                if ($resWF->rowCount()) {
                                    echo '<fieldset class = "divGroupeCritere subSection" style = "border-radius:15px; padding-left:25px;">
                                        ';
                                    echo '<table align = "center" width=98% style="font-size: 14px;">';
                                    echo '<tr><th>Date Action</th><th>Action</th><th>Utilisateur</th><th>Profil</th><th>Commentaire</th></tr>';
                                    while ($liWF = $resWF->fetch(PDO::FETCH_OBJ)) {
                                        echo '<tr>
                                                <td>' . formatter_date($liWF->dt_action) . '</td>
                                                <td>' . $liWF->lib_action . '</td>
                                                <td>' . $liWF->unom . ' ' . $liWF->uprenom . '</td>
                                                <td>' . $liWF->pnom . '</td>
                                                <td>' . $liWF->commentaire . '</td>
                                            </tr>';
                                    }
                                    echo '</table>';
                                    echo '</fieldset>';
                                }
                                ?>
                            </ul>
                        </div>
                    </li>
                    <li class="vo sky-tab-content-6">
                        <div class="typography" style="min-height: 200px;">
                            <ul class="uldb">
                                <?php
                                if ($cmpEtat >= CMP_ENCOURS) {
                                    require 'kpi.php';
                                } else
                                    echo '<span class="alert-box warning">Le calcul de KPI n\'est pas possible pour une campagne non active</span>';
                                ?>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </td>
        <td style="vertical-align:top"><?php echo $inptCmp; ?></td>
        <?php
        $tbRetour['exec'] = 1;
    }
} catch (PDOException $e) {
//    $tbRetour['message'] = $e->getMessage();
    echo $e->getMessage();
    print_r($e);
}
//echo json_encode($tbRetour);
?>