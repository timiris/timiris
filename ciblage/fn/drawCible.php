<?php

function drawCible($idC, $CC, $assoc_group, $connection, $idCmp = 0) {
    $tbCib = json_decode($CC, true);
//        print_r($tbCib);
    $chAnd = $chOr = '';
    if (strtolower(trim($assoc_group)) == 'or')
        $chOr = 'checked="checked"';
    else
        $chAnd = 'checked="checked"';
    echo '<div id=cntGrCiblage>';
    echo "<input type ='hidden' id = 'idCibleHidden' value ='$idC'/>";
    echo '<fieldset id="idFieldSetGroup" class="section" style = "border-radius:15px; background-color: #ddd;">
        <legend>Régle d\'association des groupes</legend>
        <input id="AssocGroupAnd" name="associationGroupe" type="radio" ' . $chAnd . ' value = "and">
        <label for="AssocGroupAnd">Tous les groupes</label>

        <input id="AssocGroupOr"  name="associationGroupe" type="radio" ' . $chOr . ' value = "or">
        <label for="AssocGroupOr">Au moins un groupe</label>
    </fieldset>';
    foreach ($tbCib as $g => $grp) {
        $g = substr($g, 1);
//        print_r($grp);
        drawGroupe($connection, $g, $grp);
    }

    echo '</div>
        <div style = "display: inline-block; position:absolute; left:20px">
            <button class="button12 black AjouterGroupe" style="display: inline-block;">+ Groupe</button>
        </div>';
}

function drawGroupe($connection, $idGroup, $grp = array()) {
    $assoc = (isset($grp['association'])) ? $grp['association'] : 'and';
    
    $cha = ("and" == $assoc) ? ' checked="checked" ' : '';
    $cho = ("or" == $assoc) ? ' checked="checked" ' : '';
    ?>
    <div id="groupe<?php echo $idGroup; ?>" class = "divGroupe dgCiblage">
        <fieldset id="divGroupeCritere<?php echo $idGroup; ?>" class="divGroupeCritere subSection"  style = "border-radius:25px;">
            <legend>Groupe de critéres</legend>
            <div class="SupprimerDIV" title="Supprimer le groupe" name = "groupe<?php echo $idGroup; ?>"></div>
            <div class="tgGroupMode" align="center">
                <input type="radio" class = "groupe" name="associationCritere_<?php echo $idGroup; ?>" <?= $cha; ?> id="AssocGroupAnd<?php echo $idGroup; ?>" value="and" title="">
                <label for="AssocGroupAnd<?php echo $idGroup; ?>" title="">Appliquer tout</label>
                <input type="radio" class = "groupe" name="associationCritere_<?php echo $idGroup; ?>" <?= $cho; ?> id="AssocGroupOr<?php echo $idGroup; ?>" value="or" title="" class="AssocGroupOr">
                <label for="AssocGroupOr<?php echo $idGroup; ?>" title="">Appliquer au moins un</label>
            </div>

            <div id = "critereContent<?php echo $idGroup; ?>">
                <?php
                if (isset($grp['association']))
                    unset($grp['association']);
                foreach ($grp as $idc => $cr) {
//                    print_r($grp);
                    $idc = substr($idc, 1);
                    drawCritere($connection, $idGroup, $idc, $cr);
                }
                ?>
            </div>
            <div align="center" style = "border : 1px solid blue; padding:10px; border-radius:15px;">
                <label for="idSelectNatureTrafic<?php echo $idGroup; ?>">Nature du trafic : </label>
                <select id="idSelectNatureTrafic<?php echo $idGroup; ?>" class="selectNatureTrafic">
                    <option value = ""> </option>
                    <?php
                    $req = "SELECT * FROM ref_nature WHERE etat = 1 and ciblage = true order by libelle";
                    // ORDER BY type ASC, poids DESC
                    try {
                        $result = $connection->query($req);
                        if ($result->rowCount()) {
                            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                echo "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                            }
                        }
                    } catch (PDOException $e) {
                        $retour["message"] = $e->getMessage();
                    }
                    ?>
                </select>
                <label for="idSelectTypeDonnee<?php echo $idGroup; ?>">Type données : </label>
                <select id="idSelectTypeDonnee<?php echo $idGroup; ?>" class="selectTypeDonnee " style = "width: 180px;">
                    <option value = ""> </option>
                    <?php
                    //require_once "type_donnees.php";
                    ?>
                </select>
                <button id="AjouterCritere<?php echo $idGroup; ?>" class="button12 black ajouterCritere">+ Critére</button>
            </div>
        </fieldset>
    </div>
    <?php
}

function drawCritere($connection, $idGroup, $idc, $cr = array()) {
    global $libNature, $libTypeDonnees;
    $nat = explode('_', $cr['natureType']);
    $tp_dn = $nat[1];
    $nature = $nat[0];
    $idDOM = $idGroup . '_' . $idc;

    $nat_tr_txt = $libNature{$nature};
    $tp_dn_txt = $libTypeDonnees[$tp_dn]['libelle'];
    $titreCritere = "Critére : {Nature Trafic : <b>" . $nat_tr_txt . "</b>}/{Type données : <b>" . $tp_dn_txt . "</b>}";
    ?>

    <div id="critere_<?php echo $idDOM; ?>" class = "divCritere" style = "border :1px solid blue; margin-top:5px; border-radius:15px;">
        <span class = 'entCritere'><?php echo $titreCritere; ?></span>
        <span class="SupprimerDIV" name = "critere_<?php echo $idDOM; ?>" title="Supprimer le critére"></span>
        <span class="reduireDIV reduire" name = "critere_<?php echo $idDOM; ?>" title="Réduire">
            <img src = "img/moins.png" name = "img_reduire_critere_<?php echo $idDOM; ?>" width = "16" height = "16"/>
        </span>
        <div name = "divContent_critere_<?php echo $idDOM; ?>">
            <input type="hidden" id="natureType_<?php echo $idDOM; ?>" class="critere" value = "<?php echo $cr['natureType']; ?>"/>
            <?php
            if ($nature != NATURE_ATTRIBUT)
                drawCritereNotAttribut($connection, $idDOM, $tp_dn, $cr);
            else
                drawCritereAttribut($connection, $idDOM, $tp_dn, $cr);
            ?>
        </div>
    </div>
    <?php
}

function drawCritereNotAttribut($connection, $idDOM, $tp_dn, $cr = array()) {
    global $lib, $libTypeDonnees;
    $options = $selUnite = "";
    $arr_inv_op = array('!=' => '=', '=' => '!=', '<' => '>=', '>' => '<=', '<=' => '>', '>=' => '<');
    $limit = array('j' => 31, 'm' => 12, 'a' => 4);
    $label = array('j' => '31 Jours', 'm' => '12 Mois', 'a' => '4 Ans');
    $periode = (isset($cr['idUnitePeriodique'])) ? $cr['idUnitePeriodique'] : 'j';
    $idFormule = (isset($cr['idFormule'])) ? $cr['idFormule'] : '';
    $operateur = (isset($cr['operateur'])) ? $cr['operateur'] : '';
    if(isset($cr['estInverse']) && ($cr['estInverse']) && isset($arr_inv_op[trim($operateur)]))
        $operateur = $arr_inv_op[trim($operateur)];
    $valeurCritere = (isset($cr['valeurCritere'])) ? $cr['valeurCritere'] : '';
    $untieValeur = (isset($cr['untieValeur'])) ? $cr['untieValeur'] : '';
    $idTypeCompteur = (isset($cr['idTypeCompteur'])) ? $cr['idTypeCompteur'] : '';
    $relPeriodeFrom = (isset($cr['relPeriodeFrom'])) ? $cr['relPeriodeFrom'] : ($limit[$periode] - 1);
    $relPeriodeTo = (isset($cr['relPeriodeTo'])) ? $cr['relPeriodeTo'] : 0;

    $idPeriodeFrom = getDateRelInv($relPeriodeFrom, $periode);
    $idPeriodeTo = getDateRelInv($relPeriodeTo, $periode);

    $selectj = $selectm = $selecta = '';
    ${'select' . $periode} = 'selected';
    ?>
    <table width = "95%">
        <tbody>
            <tr>
                <td><label for="idTypeCompteur_<?php echo $idDOM; ?>"><?php echo $lib['idTypeCompteur']; ?> : </label></td>
                <td>
                    <select id="idTypeCompteur_<?php echo $idDOM; ?>" class="critere">
                        <?php
                        $req = "SELECT * FROM ref_compteurs WHERE etat = '1' and fk_id_type='" . $tp_dn . "' ORDER BY libelle";
                        // ORDER BY type ASC, poids DESC
                        try {
                            $result = $connection->query($req);
                            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                if ($idTypeCompteur == $ligne->code_cmpt)
                                    $options .= "<option value = " . strtolower($ligne->code_cmpt) . " selected>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                else
                                    $options .= "<option value = " . strtolower($ligne->code_cmpt) . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                            }
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        echo $options;
                        ?>
                    </select>
                </td>
                <td><label for="idUnitePeriodique_<?php echo $idDOM; ?>"><?php echo $lib['idUnitePeriodique']; ?> : </label></td>
                <td>
                    <select id="idUnitePeriodique_<?php echo $idDOM; ?>" class="unite_periodique critere">
                        <option value="j" <?= $selectj; ?>>Jour</option>
                        <option value="m" <?= $selectm; ?>>Mois</option>
                        <option value="a" <?= $selecta; ?>>Année</option>
                    </select>
                    <span id="idLibellePeriode_<?php echo $idDOM; ?>" style = "margin-left:15px;">  (<?= $label[$periode]; ?>)</span>
                </td>
            </tr>
            <tr>
                <td><label for="idPeriodeFrom_<?php echo $idDOM; ?>"><?php echo $lib['idPeriodeFrom']; ?> : </label></td>
                <td>
                    <select id="idPeriodeFrom_<?php echo $idDOM; ?>" class = "critere select_for_periode">
                        <?php
                        $optionsFrom = $optionsTo = "";
                        $req = "SELECT * FROM historique_correspondance WHERE type = '$periode' ORDER BY h_date LIMIT " . $limit[$periode];
// ORDER BY type ASC, poids DESC
                        try {
                            $result = $connection->query($req);
                            if ($result->rowCount()) {
                                $nbRestant = $result->rowCount();
                                while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                    $nbRestant--;
                                    if ($idPeriodeFrom == $ligne->h_date)
                                        $optionsFrom .= "<option value = '" . $ligne->h_date . "' selected>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
                                    else
                                        $optionsFrom .= "<option value = '" . $ligne->h_date . "'>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
                                    if ($idPeriodeTo == $ligne->h_date)
                                        $optionsTo .= "<option value = '" . $ligne->h_date . "' selected>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
                                    else
                                        $optionsTo .= "<option value = '" . $ligne->h_date . "'>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
                                    // $options .= "<option value = ".$ligne->champ.">".ucfirst(strtolower($ligne->h_date))."</option>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        echo $optionsFrom;
                        ?>
                    </select>
                    <span style = "margin-left:20px; margin-right:20px;"> <?php echo $lib['idPeriodeTo']; ?> </span>
                    <select id="idPeriodeTo_<?php echo $idDOM; ?>" class = "critere select_for_periode">
                        <?php
                        echo $optionsTo;
                        ?>
                    </select>
                </td>
                <td><label for="idFormule_<?php echo $idDOM; ?>"><?php echo $lib['idFormule']; ?> : </label></td>
                <td>
                    <?php
                    $s_Formule = array('least' => '', 'greatest' => '', 'SUM' => '', 'AVG' => '');
                    $s_Formule[$idFormule] = ' selected';
                    ?>
                    <select id="idFormule_<?php echo $idDOM; ?>" class = "critere">
                        <option value="least" <?= $s_Formule['least']; ?>>Minimum</option>
                        <option value="greatest" <?= $s_Formule['greatest']; ?>>Maximum</option>
                        <option value="SUM" <?= $s_Formule['SUM']; ?>>Somme</option>
                        <option value="AVG" <?= $s_Formule['AVG']; ?>>Moyenne</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="operateur_<?php echo $idDOM; ?>"><?php echo $lib['operateur']; ?> : </label>
                </td>
                <td colspan ="3">
                    <?php
                    $s_Operateur = array('=' => '', '!=' => '', '>=' => '', '>' => '', '<=' => '', '<' => '');
                    $s_Operateur[trim($operateur)] = ' selected';
                    ?>
                    <select id="operateur_<?php echo $idDOM; ?>" class="critere">
                        <option value=" = " <?= $s_Operateur['=']; ?>>Egal</option>
                        <option value=" != " <?= $s_Operateur['!=']; ?>>Différent de</option>
                        <option value=" >= " <?= $s_Operateur['>=']; ?>>Sup ou Egal</option>
                        <option value=" > " <?= $s_Operateur['>']; ?>>Supérieur à </option>
                        <option value=" <= " <?= $s_Operateur['<=']; ?>>Inf ou Egal</option>
                        <option value=" < " <?= $s_Operateur['<']; ?>>Inférieur de </option>
                    </select>
                    <input type="text" id="valeurCritere_<?php echo $idDOM; ?>" value ="<?= $valeurCritere; ?>" size = "8" class = "critere chiffre">
                    <?php
// var_dump($tabUnite);
//                    $req = "SELECT unite FROM ref_type_donnee WHERE id = " . $tp_dn;
//                    $result = $connection->query($req);
//                    $ligne = $result->fetch(PDO::FETCH_OBJ);
//                    $uniteTab = json_decode($ligne->unite, true);
                    $uniteTab = $libTypeDonnees[$tp_dn]['unite'];
                    if (count($uniteTab))
                        $df_unt = array(60, 100, 1048576);
                    foreach ($uniteTab as $u => $l) {
                        if (($untieValeur == '' && in_array($u, $df_unt)) || ($untieValeur != '' && $untieValeur == $u))
                            $sel = ' selected ';
                        else
                            $sel = '';
                        $selUnite .= '<option value =' . $u . ' ' . $sel . '>' . $l . '</option>';
                    }
                    echo '<SELECT style = "margin-left:20px;" id="untieValeur_' . $idDOM . '" class="critere">' . $selUnite . '</SELECT>';
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

function drawCritereAttribut($connection, $idDOM, $tp_dn, $cr = array()) {
    global $lib;
    $idTypeCompteur = isset($cr['idTypeCompteur']) ? $cr['idTypeCompteur'] : '';
    $arr_inv_op = array('!=' => '=', '=' => '!=', '<' => '>=', '>' => '<=', '<=' => '>', '>=' => '<');
    $operateur = isset($cr['operateur']) ? $cr['operateur'] : '';
    if(isset($cr['estInverse']) && ($cr['estInverse']) && isset($arr_inv_op[$operateur]))
        $operateur = $arr_inv_op[$operateur];        
    $valeurCritere = isset($cr['valeurCritere']) ? $cr['valeurCritere'] : '';
    ?>
    <table width = "95%">
        <tbody>
            <tr>
                <td><label for="idTypeCompteur_<?php echo $idDOM; ?>"><?php echo $lib['idTypeCompteur']; ?></label></td>
                <td>
                    <select id="idTypeCompteur_<?php echo $idDOM; ?>" class="critere champCiblage">
                        <?php
                        $options = "";
                        $req = "SELECT * FROM ref_attribut WHERE type='" . $tp_dn . "' AND etat = 1 order by libelle";
                        try {
                            $result = $connection->query($req);
                            if ($result->rowCount()) {
                                $ligne = $result->fetch(PDO::FETCH_OBJ);
                                $firstChamp = $ligne->categorie;
                                $firstClass = $ligne->classe;
                                $html = $ligne->html;
                                $maxlength = ($ligne->max_length) ? " maxlength = " . $ligne->max_length : "";
                                $options .= "<option value = " . $ligne->code . ":" . $ligne->categorie . ":" . $ligne->html . ":" . $ligne->max_length . ":" . $ligne->classe . ":" . $ligne->type . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                    if ($idTypeCompteur == $ligne->code) {
                                        $firstChamp = $ligne->categorie;
                                        $firstClass = $ligne->classe;
                                        $html = $ligne->html;
                                        $maxlength = ($ligne->max_length) ? " maxlength = " . $ligne->max_length : "";
                                        $options .= "<option value = " . $ligne->code . ":" . $ligne->categorie . ":" . $ligne->html . ":" . $ligne->max_length . ":" . $ligne->classe . ":" . $ligne->type . " selected>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                    } else
                                        $options .= "<option value = " . $ligne->code . ":" . $ligne->categorie . ":" . $ligne->html . ":" . $ligne->max_length . ":" . $ligne->classe . ":" . $ligne->type . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        echo $options;
                        ?>
                    </select>
                </td>
                <td><label for="operateur_<?php echo $idDOM; ?>"><?php echo $lib['operateur']; ?></label></td>
                <td>
                    <select id="operateur_<?php echo $idDOM; ?>" class="critere operateur_ch_ciblage">
                        <?php
                        $options = "";
                        $req = "SELECT * FROM ref_categorie_operation WHERE code='" . $firstChamp . "' ";
                        try {
                            $result = $connection->query($req);
                            if ($result->rowCount()) {
                                while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                    if ($operateur == $ligne->operateur)
                                        $options .= "<option value = '" . $ligne->operateur . "' selected>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                    else
                                        $options .= "<option value = '" . $ligne->operateur . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        echo $options;
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="valeurCritere_<?php echo $idDOM; ?>"><?php echo $lib['valeurCritere']; ?> </label></td>
                <td colspan ="3">
                    <div id = "divValRecherchee_<?php echo $idDOM; ?>">
                        <?php
                        if ($html == 'input') {
                            echo "<input type = 'text' value ='$valeurCritere' id = 'valeurCritere_$idDOM' $maxlength class='critere $firstClass'/>";
                        } else {  // SELECT
                            echo "<SELECT id = 'valeurCritere_$idDOM' class='critere'>";
                            $result = $connection->query("SELECT * FROM ref_liste_choix_attribut WHERE attribut = '$idTypeCompteur'");
                            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                if ($valeurCritere == $ligne->code)
                                    echo "<option value = '" . $ligne->code . "' selected>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                                else
                                    echo "<option value = '" . $ligne->code . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                            }
                            echo '</SELECT>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}
?>
