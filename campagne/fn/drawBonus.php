<?php
if (!isset($rep))
    $rep = "../../";
require_once $rep . "lib/tbLibelle.php";
require_once $rep . "campagne/bonus/config.php";

function drawBonus($type, $arrGrp, $arrBns, $connection, $sms_ar, $sms_fr) {
    echo '<div id = "cntGrEvent">
    <fieldset class="section" style = "border-radius:15px; background-color: #ddd;">
        <legend>Régle d\'association des groupes</legend>
        <input id="AssocGroupDeclench"  name="AssocGroupDeclench" type="radio" value = "or" checked>
        <label for="AssocGroupDeclench">Au moins un groupe</label>
    </fieldset>';

    $cls = "";
    if ($type == 'evenement') {
        $cls = 'event';
    }
//    require_once "groupe.php";
    foreach ($arrGrp as $idGrp => $grp)
        drawGroupe($idGrp, $connection, $cls, $grp, $arrBns);

    echo '</div>';
    echo '<div>
        <div style = "display: inline-block; position:absolute; left:20px">
            <button  class="button12 black declencheur ' . $cls . ' AjouterGroupe" style="display: inline-block;">+ Groupe</button>
        </div>
    </div><br><br>';
    echo '<div id="cntBonus">';
    echo drawBonusGroup('bnsgeneral', $arrBns, 1, $sms_ar, $sms_fr);
    echo '</div>';
}

function drawGroupe($idGroup, $connection, $cls, $grp, $arrBns) {
//    $nature = (count($grp)) ? $grp['nature'] : '';
    ?>
    <div id="groupe<?php echo $idGroup; ?>" class = "divGroupe dgDeclencheur">
        <fieldset id="divGroupeCritere<?php echo $idGroup; ?>" class="divGroupeCritere subSection"  style = "border-radius:25px;">
            <legend>Groupe de critéres</legend>
            <div class="SupprimerDIV" title="Supprimer le groupe" name = "groupe<?php echo $idGroup; ?>"></div>
            <div class="tgGroupMode" align="center">
                <input type="radio" class = "groupe" name="associationCritere_<?php echo $idGroup; ?>" checked="checked" id="AssocGroupAnd<?php echo $idGroup; ?>" value="and" title="">
                <label for="AssocGroupAnd<?php echo $idGroup; ?>" title="">Appliquer tout</label>
            </div>
            <div id = "critereContent<?php echo $idGroup; ?>">
                <?php
                if ((count($grp))) {
                    $nature = $grp['nature'];
                    foreach ($grp['declencheur'] as $idDec => $dec) {
                        drawDeclencheur($nature, $idGroup, $idDec, $dec, $connection, $cls);
                    }
                } else
                    $nature = '';
                ?>
            </div>
            <div align="center" style = "border : 1px solid blue; padding:10px; border-radius:15px;">
                <label for="idSelectNatureTrafic<?php echo $idGroup; ?>">Nature du trafic : </label>
                <select id="idSelectNatureTrafic<?php echo $idGroup; ?>" class="selectNatureTrafic <?php echo $cls; ?>">
                    <?php
                    echo getNature($connection, $nature);
                    ?>
                </select>
                <label for="idSelectTypeDonnee<?php echo $idGroup; ?>">Type données : </label>
                <select id="idSelectTypeDonnee<?php echo $idGroup; ?>" class="selectTypeDonnee event" style = "width: 180px;">
                    <?php
                    echo getTypeDonnees($connection, $nature);
                    ?>
                </select>
                <button id="AjouterCritere<?php echo $idGroup; ?>" class="button12 black ajouterCritere declencheur <?php echo $cls; ?>">+ Critére</button>
            </div>
            <?php
            //require_once '../bonus/bonus_groupe.php';
            echo drawBonusGroup($idGroup, $arrBns, $nature, $grp['sms_ar'], $grp['sms_fr']);
            ?>
        </fieldset>
    </div>

    <?php
}

function getNature($connection, $nature = '') {
    $ret = '';
    if ($nature == '') {
        $ret .= '<option value = ""> </option>';
        $cnd = ' etat = 1  and event = true ';
    } else
        $cnd = " id = $nature ";
    $req = "SELECT * FROM ref_nature WHERE $cnd order by libelle";
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ))
            $ret .= "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
    }
    return $ret;
}

function getTypeDonnees($connection, $nature = '') {
    $ret = '';
    if ($nature == '') {
        $ret = '<option value = ""> </option>';
        return $ret;
    }
    $req = "SELECT * FROM ref_type_donnee_event WHERE etat = 1 AND fk_id_nature = '" . $nature . "' order by id";
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ))
            $ret .= "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
    }
    return $ret;
}

function drawDeclencheur($nature, $idGroup, $idDec, $dec, $connection, $cls) {
    global $libNature, $libTypeDonnees, $libTypeDonneesEvent;
    $idDOM = $idGroup . '_' . $idDec;
    if ($dec["fk_id_td"]) {
        $tp_dn = $dec["fk_id_td"];
        $tp_dnLib = $libTypeDonnees[$tp_dn]['libelle'];
    } else {
        $tp_dn = $dec["fk_id_td_event"];
        $tp_dnLib = $libTypeDonneesEvent[$tp_dn]['libelle'];
    }
    $table = $nature . '_' . $tp_dn;
    $titreCritere = "{Nature Trafic : <b>" . $libNature[$nature] . "</b>}/{Type données : <b>" . $tp_dnLib . "</b>}";
    ?>  
    <div id="critere_<?php echo $idDOM; ?>" class = "divCritere" style = "border :1px solid blue; margin-top:5px; border-radius:15px;">
        <span class = 'entCritere'><?php echo $titreCritere; ?></span>
        <span class="SupprimerDIV" name = "critere_<?php echo $idDOM; ?>" title="Supprimer le critére"></span>
        <span class="reduireDIV reduire" name = "critere_<?php echo $idDOM; ?>" title="Réduire">
            <img src = "img/moins.png" name = "img_reduire_critere_<?php echo $idDOM; ?>" width = "16" height = "16"/>
        </span>
        <div name = "divContent_critere_<?php echo $idDOM; ?>">
            <input type="hidden" id="natureType_<?php echo $idDOM; ?>" class="critere" value = "<?php echo $table; ?>"/>
            <?php
            //require 'critereEvent.php';
            drawDeclencheurEvent($connection, $idDOM, $tp_dn, $dec);
            ?>
        </div>
    </div>
    <?php
}

function drawBonusGroup($idGroup, $arrBns, $natureTrafic, $sms_ar, $sms_fr) {
    $idBonus = $idGroup;
    $idGroup = (int) $idGroup;
    $legendBONUS = ($idBonus == 'bnsgeneral' || $idBonus == 1000) ? 'Bonus Global' : 'Bonus du groupe';
    $str = '<div id="Bonus' . $idBonus . '" class = "divBonus">
        <fieldset id="divGroupeBonus' . $idBonus . '" class="divGroupeCritere subSection"  style = "border-radius:25px;">
            <legend>' . $legendBONUS . '</legend>
            <div class="ajouterDIV" name ="ajouterDiv' . $idBonus . '"title="Ajouter Bonus"></div>
            <div style="margin-bottom: 5px;">';
    $labelBONUS = ($idBonus == 'bnsgeneral' || $idBonus == 1000) ? 'SMS COMMUNICATION BONUS' : 'SMS COMMUNICATION BONUS GROUPE';
    if ($sms_ar == '' && $sms_fr == '') {
        $display = ' display:none;';
        $checked = '';
    } else {
        $display = '';
        $checked = 'checked';
    }
    $str .= '<input type="checkbox" class="ShowSMSBonus" id="idShowSMSBonus' . $idBonus . '" ' . $checked . '>';
    $str .= '<label for="idShowSMSBonus' . $idBonus . '"> ' . $labelBONUS . '</label><br>';
    $str .= '<textarea rows="2" cols="40" name="smsTeasignFr' . $idBonus . '" class="smsComms" style="' . $display . '" id="idSMSBonusFr' . $idBonus . '" placeholder ="SMS de communication en français">' . $sms_fr . '</textarea>';
    $str .= '<textarea rows="2" cols="40" name="smsTeasignAr' . $idBonus . '" class="arabic smsComms" dir="rtl" style="' . $display . '" id="idSMSBonusAr' . $idBonus . '" placeholder="رسالة نصية باللغة العربية">' . $sms_ar . '</textarea>';
    $str .= '<br><center><span align="right" name="smsTeasignAr' . $idBonus . 'Span"></span><span name="smsTeasignFr' . $idBonus . 'Span"></span></center>';
    $str .= '<br />';
    $str .= '<ul class="tags" id ="tags_' . $idBonus . '" style="' . $display . '">
        <li><b>Variables :&nbsp;&nbsp;</b></li>
        <li class="tagBns" name="{$msisdn}">Numéro</li>
        <li class="tagBns" name="{$nom}">Nom</li>
        <li class="tagBns" name="{$solde}">Solde</li>
        <li class="tagBns" name="{$sfidelity}">Solde fidélité</li>
        <li class="tagBns" name="{$bns_values}">Bonus valeur</li>
    </ul>
    </div>';
    if (isset($arrBns[$idGroup]))
        foreach ($arrBns[$idGroup] as $idBns => $bns)
            $str .= Bonus($idBns, $bns, $natureTrafic, $idGroup);
    $str .= '</fieldset></div>';
    return $str;
}

function getTypeBonus($type, $def = '') {
    global $connection;
    $str = '';
    $req = "select * from ref_type_bonus where etat = 1 order by id";
    $result = $connection->query($req);
    $ligne = $result->fetch(PDO::FETCH_OBJ);
    $str .= '<option value = "' . $ligne->id . '">' . $ligne->libelle . '</option>';
    if ($type != 'libre') {
        $ligne = $result->fetch(PDO::FETCH_OBJ);
        if ($def == $ligne->id)
            $str .= '<option value = "' . $ligne->id . '" SELECTED>' . $ligne->libelle . '</option>';
        else
            $str .= '<option value = "' . $ligne->id . '">' . $ligne->libelle . '</option>';
    }
    return $str;
}

function getCompteursBonus($idType = '', $def = '') {
    global $connection;
    $str = '';
    $req = 'select code_cmpt as id, libelle from ref_compteurs where fk_id_type = ' . (int) $idType . ' and bonus = true order by libelle';
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            $id = (int) $ligne->id;
            if ($id) {
                if ($id == $def)
                    $str .= "<option value = " . $ligne->id . " SELECTED>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                else
                    $str .= "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
            }
        }
    }
    return $str;
}

function getUnitBonus($type, $natBonus, $natureTrafic, $typeDec, $def) {
    global $connection, $conf_bonus_pp_cumule, $conf_bonus_pp;
    $natBonus = (int) $natBonus;
    $str = '';
    if ($type == 1) {
        $req = 'select unite from ref_nature_bonus where id = ' . (int) $natBonus;
        $result = $connection->query($req);
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_OBJ);
            $uniteTab = json_decode($ligne->unite, true);
            $defUnits = array(60, 100, 1048576);
            if (count($uniteTab))
                foreach ($uniteTab as $u => $l) {
                    $sel = (($def != '' && $u == $def) || ($def == '' && in_array($u, $defUnits))) ? ' selected ' : '';
                    $str .= '<option value =' . $u . ' ' . $sel . '>' . $l . '</option>';
                }
        }
    } else {
        $natureTrafic = (int) $natureTrafic;
        $arrConf = (substr($typeDec, 0, 6) == 'cumule') ? $conf_bonus_pp_cumule : $conf_bonus_pp;
        if ($natureTrafic)
            foreach ($arrConf[$natureTrafic][$natBonus] as $k => $v) {
                if ($k == $def)
                    $str .= '<option value =' . $k . ' SELECTED>' . $v . '</option>';
                else
                    $str .= '<option value =' . $k . ' >' . $v . '</option>';
            }
    }
    return $str;
}

function getNatureBonus($type = '', $def = '') {
    global $connection, $conf_bonus_pp, $conf_bonus_pp_cumule;
    $str = $cnd = '';
    if ($type == 2) {
        $cnd = ' AND id in (' . implode(',', array_keys($conf_bonus_pp[$type])) . ') ';
    }

//    $str .= '<option value = ""> </option>';
    $req = "SELECT * FROM ref_nature_bonus WHERE etat = 1 $cnd order by id desc";
    // ORDER BY type ASC, poids DESC
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            if ($def == $ligne->id)
                $str .= "<option value = " . $ligne->id . " SELECTED>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
            else
                $str .= "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
        }
    }
    return $str;
}

function Bonus($idBonus, $bns, $natureTrafic, $idGroup) {
//    $ex = array('type_bonus' => $bns->type_bonus, 'nature' => $bns->nature,
//        'code_bonus' => $bns->code_bonus, 'valeur' => $bns->valeur, 'ch_ref' => $bns->ch_ref,
//        'unite' => $bns->unite);
    $str = '';
    global $connection, $defDeclencheur;
    $type = ($defDeclencheur == 'fidelite' || $idGroup == 0) ? 'libre' : '';
    $str .= "<div id ='divCntBonus$idBonus' class='divCntBonus'>";
    $str .= '<div class="SupBonus" title="Supprimer le Bonus" name = "Bonus' . $idBonus . '"></div>';
    $str .= '<div align = "center" style = "border : 1px solid blue; margin-bottom: 5px; padding:10px; border-radius:15px;">';
    $str .= '<select id = "idTypeBonus' . $idBonus . '" style = "width:120px;" class = "selectTypeBonus">';
    $str .= getTypeBonus($type, $bns['type_bonus']);
    $str .= '</select>';

    $str .= '<label for="idSelectNatureBonus' . $idBonus . '">Nature Bonus : </label>';
    $str .= '<select id="idSelectNatureBonus' . $idBonus . '" class="selectNatureBonus" style="width:120px;">';
    $str .= getNatureBonus($bns['type_bonus'], $bns['nature']);
    $str .= '</select>';
    $str .= '<label for="idSelectCompteur' . $idBonus . '" id="label' . $idBonus . '">Compteur : </label>';
    $str .= '<select id="idSelectCompteur' . $idBonus . '" class="selectCompteurBonus" style="width:200px;">';
    $str .= getCompteursBonus($bns['nature'], $bns['code_bonus']);
    $str .= '</select>';
    $str .= '<label for="idValeurBonus' . $idBonus . '">Valeur : </label>';
    $str .= '<input type="text" id ="idValeurBonus' . $idBonus . '" value ="' . $bns['valeur'] . '" class="chiffre" style="width:50px;"/>';
    $str .= '<select id="idUniteCompteur' . $idBonus . '"  style="width:100px;">';
    $str .= getUnitBonus($bns['type_bonus'], $bns['nature'], $natureTrafic, $defDeclencheur, $bns['unite']);
    $str .= '</select>';
    $str .= '</div>';
    $str .= '</div>';

    return $str;
}

function drawDeclencheurEvent($connection, $idDOM, $tp_dn, $dec) {
    //print_r($dec);
    ?>
    <table width = "95%"style ='margin-top:5px;'>
        <tbody>
            <tr>
                <td style='width:100px'><label for="idTypeCompteur_<?php echo $idDOM; ?>">Critère : </label></td>
                <td>
                    <select id="idTypeCompteur_<?php echo $idDOM; ?>" class="critere champCiblage" style='width:250px'>
                        <?php
                        $options = $categorie = "";
                        $defOption = isset($dec['code_declencheur']) ? $dec['code_declencheur'] : '';
                        if (isset($dec['fk_id_td_event']) && !empty($dec['fk_id_td_event']))
                            $req = "SELECT * FROM ref_event WHERE type=" . $tp_dn . " AND etat = 1 order by libelle";
                        else
                            $req = "SELECT libelle, 2 as categorie, 'input' as html, 'chiffre' as classe, code_cmpt as code, fk_id_type as type, 10 as max_length FROM ref_compteurs WHERE fk_id_type='" . $tp_dn . "' AND etat = 1 order by libelle";
                        try {
                            $result = $connection->query($req);
                            if ($result->rowCount()) {
                                $ligne = $result->fetch(PDO::FETCH_OBJ);
                                $html = $ligne->html;
                                $code = $ligne->code;
                                $class = $ligne->classe;
                                $categorie = $ligne->categorie;
                                $maxlength = $ligne->max_length;
                                $valueFirst = $ligne->code . ":" . $categorie . ":" . $html . ":" . $maxlength . ":" . $class . ":" . $ligne->type . ":declencheur";
                                $options .= "<option value = '$valueFirst'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                    if ($defOption == $ligne->code) {
                                        $options .= "<option SELECTED value = " . $ligne->code . ":" . $ligne->categorie . ":" . $ligne->html . ":" . $ligne->max_length . ":" . $ligne->classe . ":" . $ligne->type . ":declencheur>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                        $html = $ligne->html;
                                        $code = $ligne->code;
                                        $class = $ligne->classe;
                                        $categorie = $ligne->categorie;
                                        $maxlength = $ligne->max_length;
                                    } else
                                        $options .= "<option value = " . $ligne->code . ":" . $ligne->categorie . ":" . $ligne->html . ":" . $ligne->max_length . ":" . $ligne->classe . ":" . $ligne->type . ":declencheur>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                }
                            } else {
                                $maxlength = $class = $valueFirst = "";
                            }
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        echo $options;
                        ?>
                    </select>
                    <select id="operateur_<?php echo $idDOM; ?>" class="critere operateur_ch_ciblage">
                        <?php
                        //require_once $rep . 'ciblage/val_categorie_operation.php';
                        $opp = (isset($dec['operateur'])) ? $dec['operateur'] : '';
                        echo val_categorie_operation($connection, $categorie, $opp);
                        ?>
                    </select>
                    <div id = "divValRecherchee_<?php echo $idDOM; ?>" style="display:inline">
                        <?php
//                        $_POST["id"] = $idDOM;
                        //require_once $rep . 'ciblage/val_recherchee.php';
                        $val = (isset($dec['valeur'])) ? $dec['valeur'] : '';
                        echo val_recherchee($html, $maxlength, $idDOM, $class, $code, $connection, $val);
                        ?>
                    </div>
                    <div id = "divUniteValRecherchee_<?php echo $idDOM; ?>" style="display:inline">
                        <?php
// var_dump($tabUnite);
                        $selUnite = "";
                        //if (!empty($_POST["evnt"]))
                        $defUnit = (isset($dec["unite"])) ? $dec["unite"] : '';
                        if (isset($dec["fk_id_td"]) && !empty($dec["fk_id_td"]))
                            $req = "SELECT cmpt_parent, unite FROM ref_type_donnee WHERE id = " . $tp_dn;
                        else
                            $req = "SELECT cmpt_parent, unite FROM ref_type_donnee_event WHERE id = " . $tp_dn;
                        $result = $connection->query($req);
                        $ligne = $result->fetch(PDO::FETCH_OBJ);
                        $uniteTab = json_decode($ligne->unite, true);
                        $cmpt_parent = $ligne->cmpt_parent;
                        if (count($uniteTab))
                            foreach ($uniteTab as $u => $l) {
                                $sel = (($defUnit != '' && $defUnit == $u) || ($defUnit == '' && ($u == 60 || $u == 100 || $u == 1048576))) ? ' SELECTED ' : '';
                                $selUnite .= '<option value =' . $u . ' ' . $sel . '>' . $l . '</option>';
                            }
                        if ($selUnite != "")
                            echo '<SELECT style = "margin-left:20px;" id="untieValeur_' . $idDOM . '" class="critere">' . $selUnite . '</SELECT>';
                        ?>
                    </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

function val_categorie_operation($connection, $categorie, $opp) {
    $options = "";
    $categorie = (int) $categorie;
    if ($categorie) {
        $opp = strtolower($opp);
        $req = "SELECT * FROM ref_categorie_operation WHERE code= $categorie ";
        $result = $connection->query($req);
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            if ($opp == strtolower($ligne->operateur))
                $options .= "<option value = '" . $ligne->operateur . "' SELECTED>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
            else
                $options .= "<option value = '" . $ligne->operateur . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
        }
    }
    return $options;
}

function val_recherchee($html, $maxlength, $id, $class, $code, $connection, $valeur) {
    $divValRech = "";
    if (strtolower($html) == 'input') {
        $max = ($maxlength != '') ? 'maxlength = "' . $maxlength . '"' : '';
        $divValRech = '<input type = "text" id = "valeurCritere_' . $id . '" ' . $max . '  value = "' . $valeur . '" size ="6" class = "critere ' . $class . '" />';
    } elseif (strtolower($html) == 'select') {
        $req = "SELECT * FROM ref_liste_choix_attribut WHERE attribut = '$code'";
        $result = $connection->query($req);
        $valeur = explode('|', $valeur);
        $divValRech = '<SELECT id = "valeurCritere_' . $id . '" class = "critere ' . $class . '">';
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            if (in_array($ligne->code, $valeur))
                $divValRech .= "<option value = '" . $ligne->code . "' SELECTED>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
            else
                $divValRech .= "<option value = '" . $ligne->code . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
        }
        $divValRech .= "</SELECT>";
        if ($class == 'multiple') {
            $valeur = '"' . implode('", "', $valeur) . '"';
            echo '<script>'
            . '$("#valeurCritere_' . $id . '").multipleSelect();'
            . '$("#valeurCritere_' . $id . '").multipleSelect("setSelects", [' . $valeur . ']);'
            . '</script>';
        }
    }
    return $divValRech;
}
?>
