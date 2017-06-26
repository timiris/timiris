<?php
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
$idGroup = 100;
if (isset($_POST["idGroup"]) and !empty($_POST["idGroup"])) {
    $idGroup = $_POST["idGroup"];
}
if (isset($_POST["hEvent"]) and $_POST["hEvent"])
    $cls = 'event';
else if (!isset($cls))
    $cls = '';
?>

<div id="groupe<?php echo $idGroup; ?>" class = "divGroupe dgDeclencheur">
    <fieldset id="divGroupeCritere<?php echo $idGroup; ?>" class="divGroupeCritere subSection"  style = "border-radius:25px;">
        <legend>Groupe de critéres</legend>
        <div class="SupprimerDIV" title="Supprimer le groupe" name = "groupe<?php echo $idGroup; ?>"></div>
        <div class="tgGroupMode" align="center">
            <input type="radio" class = "groupe" name="associationCritere_<?php echo $idGroup; ?>" checked="checked" id="AssocGroupAnd<?php echo $idGroup; ?>" value="and" title="">
            <label for="AssocGroupAnd<?php echo $idGroup; ?>" title="">Appliquer tout</label>
        </div>
        <div id = "critereContent<?php echo $idGroup; ?>"></div>
        <div align="center" style = "border : 1px solid blue; padding:10px; border-radius:15px;">
            <label for="idSelectNatureTrafic<?php echo $idGroup; ?>">Nature du trafic : </label>
            <select id="idSelectNatureTrafic<?php echo $idGroup; ?>" class="selectNatureTrafic <?php echo $cls; ?>">
                <option value = ""> </option>
                <?php
                require_once $rep . "conn/connection.php";
                $req = "SELECT * FROM ref_nature WHERE etat = 1 and event = true order by libelle";
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
            <select id="idSelectTypeDonnee<?php echo $idGroup; ?>" class="selectTypeDonnee event" style = "width: 180px;">
                <option value = ""> </option>
            </select>
            <button id="AjouterCritere<?php echo $idGroup; ?>" class="button12 black ajouterCritere declencheur <?php echo $cls; ?>">+ Critére</button>
        </div>
        <?php
        require_once '../bonus/bonus_groupe.php';
        ?>
    </fieldset>
</div>
