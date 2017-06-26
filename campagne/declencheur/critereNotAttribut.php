<?php
require_once "../../fn_security.php";
check_session(); 
$tabUnite = $options = $classCmpt = $selUnite = "";
require_once "../../conn/connection.php";
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
                    <option value="j">Jour</option>
                    <option value="m">Mois</option>
                    <option value="a">Année</option>
                </select>
                <span id="idLibellePeriode_<?php echo $idDOM; ?>" style = "margin-left:15px;">  (31 Jours)</span>
            </td>
        </tr>
        <tr>
            <td><label for="idPeriodeFrom_<?php echo $idDOM; ?>"><?php echo $lib['idPeriodeFrom']; ?> : </label></td>
            <td>
                <select id="idPeriodeFrom_<?php echo $idDOM; ?>" class = "critere select_for_periode">
                    <?php
                    require_once "../conn/connection.php";
                    $optionsFrom = $optionsTo = "";
                    $req = "SELECT * FROM historique_correspondance WHERE type = 'j' ORDER BY h_date LIMIT 31";
// ORDER BY type ASC, poids DESC
                    try {
                        $result = $connection->query($req);
                        if ($result->rowCount()) {
                            $nbRestant = $result->rowCount();
                            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                                $nbRestant--;
                                if($optionsFrom == '')
                                    $optionsFrom = "<option value = '" . $ligne->h_date . "' selected>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
                                else
                                    $optionsFrom .= "<option value = '" . $ligne->h_date . "'>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
                                if($nbRestant)
                                    $optionsTo .= "<option value = '" . $ligne->h_date . "'>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
                                else
                                    $optionsTo .= "<option value = '" . $ligne->h_date . "' selected>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
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
                <select id="idFormule_<?php echo $idDOM; ?>" class = "critere">
                    <option value="least">Minimum</option>
                    <option value="greatest">Maximum</option>
                    <option value="SUM">Somme</option>
                    <option value="AVG">Moyenne</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label for="operateur_<?php echo $idDOM; ?>"><?php echo $lib['operateur']; ?> : </label>
            </td>
            <td colspan ="3">
                <select id="operateur_<?php echo $idDOM; ?>" class="critere">
                    <option value=" = ">Egal</option>
                    <option value=" != ">Différent de</option>
                    <option value=" >= ">Sup ou Egal</option>
                    <option value=" > ">Supérieur à </option>
                    <option value=" <= ">Inf ou Egal</option>
                    <option value=" < ">Inférieur de </option>
                </select>
                <input type="text" id="valeurCritere_<?php echo $idDOM; ?>" size = "8" class = "critere chiffre">
                <?php
// var_dump($tabUnite);
                $req = "SELECT unite FROM ref_type_donnee WHERE id = ".$tp_dn;
                $result = $connection->query($req);
                $ligne = $result->fetch(PDO::FETCH_OBJ);
                $uniteTab = json_decode($ligne->unite, true);
                foreach ($uniteTab as $u => $l) {
                    $sel = ($u == 60 || $u == 100 || $u == 1048576) ? ' selected ' : '';
                    $selUnite .= '<option value =' . $u . ' '.$sel.'>' . $l . '</option>';
                }
                echo '<SELECT style = "margin-left:20px;" id="untieValeur_' . $idDOM . '" class="critere">' . $selUnite . '</SELECT>';
                ?>
            </td>
        </tr>
    </tbody>
</table>