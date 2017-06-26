<?php
if (!isset($rep))
    $rep = "../";
require_once $rep . "fn_security.php";
check_session();

if (isset($_POST["idGroup"]) and !empty($_POST["idGroup"])) {
    $idGroup = $_POST["idGroup"];
}
if(!isset($idGroup))
    $idGroup = 1;
?>

<div id="groupe<?php echo $idGroup; ?>" class = "divGroupe dgCiblage">
    <fieldset id="divGroupeCritere<?php echo $idGroup; ?>" class="divGroupeCritere subSection"  style = "border-radius:25px;">
        <legend>Groupe de critéres</legend>
        <div class="SupprimerDIV" title="Supprimer le groupe" name = "groupe<?php echo $idGroup; ?>"></div>
        <div class="tgGroupMode" align="center">
            <input type="radio" class = "groupe" name="associationCritere_<?php echo $idGroup; ?>" checked="checked" id="AssocGroupAnd<?php echo $idGroup; ?>" value="and" title="">
            <label for="AssocGroupAnd<?php echo $idGroup; ?>" title="">Appliquer tout</label>
            <input type="radio" class = "groupe" name="associationCritere_<?php echo $idGroup; ?>" id="AssocGroupOr<?php echo $idGroup; ?>" value="or" title="" class="AssocGroupOr">
            <label for="AssocGroupOr<?php echo $idGroup; ?>" title="">Appliquer au moins un</label>
        </div>
        
        <div id = "critereContent<?php echo $idGroup; ?>">
            <!-- require des critées -->
        </div>
        <div align="center" style = "border : 1px solid blue; padding:10px; border-radius:15px;">
            <label for="idSelectNatureTrafic<?php echo $idGroup; ?>">Nature du trafic : </label>
            <select id="idSelectNatureTrafic<?php echo $idGroup; ?>" class="selectNatureTrafic">
                <option value = ""> </option>
                <?php
                require_once $rep . "conn/connection.php";
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
                require_once "type_donnees.php";
                ?>
            </select>
            <button id="AjouterCritere<?php echo $idGroup; ?>" class="button12 black ajouterCritere">+ Critére</button>
        </div>
    </fieldset>
</div>