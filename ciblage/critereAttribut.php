<?php
require_once "../fn_security.php";
check_session(); 
?>
<table width = "95%">
    <tbody>
        <tr>
            <td><label for="idTypeCompteur_<?php echo $idDOM; ?>"><?php echo $lib['idTypeCompteur']; ?></label></td>
            <td>
                <select id="idTypeCompteur_<?php echo $idDOM; ?>" class="critere champCiblage">
                    <?php
                    require_once "../conn/connection.php";
                    $options = "";
                    $req = "SELECT * FROM ref_attribut WHERE type='" . $tp_dn . "' AND etat = 1 order by libelle";
                    try {
                        $result = $connection->query($req);
                        if ($result->rowCount()) {
                            $ligne = $result->fetch(PDO::FETCH_OBJ);
                            $firstChamp = $ligne->categorie;
                            $firstClass = $ligne->classe;
                            $maxlength = ($ligne->max_length) ? " maxlength = " . $ligne->max_length : "";
                            $options .= "<option value = " . $ligne->code . ":" . $ligne->categorie . ":" . $ligne->html . ":" . $ligne->max_length . ":" . $ligne->classe . ":" . $ligne->type . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
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
                    <input type = "text" id = "valeurCritere_<?php echo $idDOM; ?>" <?php echo $maxlength; ?> class="critere <?php echo $firstClass; ?>"/>
                </div>
            </td>
        </tr>
    </tbody>
</table>