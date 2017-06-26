<?php
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
?>
<table width = "95%"style ='margin-top:5px;'>
    <tbody>
        <tr>
            <td style='width:100px'><label for="idTypeCompteur_<?php echo $idDOM; ?>">Critère : </label></td>
            <td>
                <select id="idTypeCompteur_<?php echo $idDOM; ?>" class="critere champCiblage" style='width:250px'>
                    <?php
                    require_once $rep . "conn/connection.php";
                    $options = "";
                    if (!empty($_POST["evnt"]))
                        $req = "SELECT * FROM ref_event WHERE type='" . $tp_dn . "' AND etat = 1 order by libelle";
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
                    require_once $rep . 'ciblage/val_categorie_operation.php';
                    echo $options;
                    ?>
                </select>
                <div id = "divValRecherchee_<?php echo $idDOM; ?>" style="display:inline">
                    <?php
                    $_POST["id"] = $idDOM;
                    require_once $rep . 'ciblage/val_recherchee.php';
                    echo $divValRech;
                    ?>
                </div>
                <div id = "divUniteValRecherchee_<?php echo $idDOM; ?>" style="display:inline">
                    <?php
// var_dump($tabUnite);
                    $selUnite = "";
                    if (!empty($_POST["evnt"]))
                        $req = "SELECT cmpt_parent, unite FROM ref_type_donnee_event WHERE id = " . $tp_dn;
                    else
                        $req = "SELECT cmpt_parent, unite FROM ref_type_donnee WHERE id = " . $tp_dn;
                    $result = $connection->query($req);
                    $ligne = $result->fetch(PDO::FETCH_OBJ);
                    $uniteTab = json_decode($ligne->unite, true);
                    $cmpt_parent = $ligne->cmpt_parent;
                    if (count($uniteTab))
                        foreach ($uniteTab as $u => $l) {
                            $sel = ($u == 60 || $u == 100 || $u == 1048576) ? ' selected ' : '';
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