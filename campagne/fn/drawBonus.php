<?php
if (!isset($rep))
    $rep = "../../";
require_once $rep . "lib/tbLibelle.php";

function drawBonus($idC, $type, $arrGrp, $arrBns, $connection) {
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
        drawGroupe($idGrp, $connection, $cls, $grp);
    echo '</div>';
}

function drawGroupe($idGroup, $connection, $cls, $grp = array()) {
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
                    if ($nature == '') {
                        echo '<option value = ""> </option>';
                        $cnd = ' etat = 1  and event = true ';
                    } else
                        $cnd = " id = $nature ";
                    $req = "SELECT * FROM ref_nature WHERE $cnd order by libelle";
                    $result = $connection->query($req);
                    if ($result->rowCount()) {
                        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                            echo "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                        }
                    }
                    ?>
                </select>
                <label for="idSelectTypeDonnee<?php echo $idGroup; ?>">Type données : </label>
                <select id="idSelectTypeDonnee<?php echo $idGroup; ?>" class="selectTypeDonnee event" style = "width: 180px;">
                    <option value = ""> </option>
                </select>
                <button id="AjouterCritere<?php echo $idGroup; ?>" class="button12 black ajouterCritere declencheur <?php echo $cls; ?>">+ Critére</button>
            </div>
            <?php
            //require_once '../bonus/bonus_groupe.php';
            drawBonusGroup($idGroup);
            ?>
        </fieldset>
    </div>

    <?php
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
            drawDeclencheurEvent($connection, $idDOM, $tp_dn, $dec)
            ?>
        </div>
    </div>
    <?php
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
                        if (count($dec))
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
                        echo val_recherchee($html, $maxlength, $html, $idDOM, $class, $code, $connection, $val);
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
    if (!$categorie)
        return $options;
    $req = "SELECT * FROM ref_categorie_operation WHERE code= $categorie ";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        if ($opp == $ligne->operateur)
            $options .= "<option value = '" . $ligne->operateur . "' SELECTED>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
        else
            $options .= "<option value = '" . $ligne->operateur . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
    }
    return $options;
}

function val_recherchee($html, $maxlength, $html, $id, $class, $code, $connection, $valeur) {
    $divValRech = "";
    if (strtolower($html) == 'input') {
        $max = ($maxlength != '') ? 'maxlength = "' . $maxlength . '"' : '';
        $divValRech = '<input type = "text" id = "valeurCritere_' . $id . '" ' . $max . '  value = "' . $valeur . '" size ="6" class = "critere ' . $class . '" />';
    } elseif (strtolower($html) == 'select') {
        $req = "SELECT * FROM ref_liste_choix_attribut WHERE attribut = '$code'";
        $result = $connection->query($req);
        $divValRech = '<SELECT id = "valeurCritere_' . $id . '" class = "critere">';
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            if ($valeur == $ligne->code)
                $divValRech .= "<option value = '" . $ligne->code . "' SELECTED>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
            else
                $divValRech .= "<option value = '" . $ligne->code . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
        }
        $divValRech .= "</SELECT>";
    }
    return $divValRech;
}

function drawBonusGroup($idGrp) {
    global $arrBns;
    return true;
}
?>
