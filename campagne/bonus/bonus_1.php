<?php
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
if (isset($_POST["idBonus"]) and !empty($_POST["idBonus"])) {
    $idBonus = $_POST["idBonus"];
}
if (!isset($idBonus))
    $idBonus = 1;
?>

<div id ='divCntBonus<?php echo $idBonus; ?>' class='divCntBonus'>
    <div class="SupBonus" title="Supprimer le Bonus" name = "Bonus<?php echo $idBonus; ?>"></div>
    <div align="center" style = "border : 1px solid blue; margin-bottom: 5px; padding:10px; border-radius:15px;">
        <select id="idTypeBonus<?php echo $idBonus; ?>" style="width:180px;" class="selectTypeBonus">
            <option value = "1">Bonus Libre</option>
            <?php
            if(isset($_POST['type_d']) && ($_POST['type_d'] != 'fidelite'))
                echo '<option value = "2">Bonus Proportionnel</option>';
            ?>
        </select>

        <label for="idSelectNatureBonus<?php echo $idBonus; ?>">Nature Bonus : </label>
        <select id="idSelectNatureBonus<?php echo $idBonus; ?>" class="selectNatureBonus" style="width:150px;">
            <option value = ""> </option>
            <?php
            require_once $rep . "conn/connection.php";
            $req = "SELECT * FROM ref_nature_bonus WHERE etat = 1 order by id desc";
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
        <label for="idSelectCompteur<?php echo $idBonus; ?>">Type : </label>
        <select id="idSelectCompteur<?php echo $idBonus; ?>" class="selectCompteurBonus" style="width:250px;">
            <option value = ""> </option>
            <?php
            //require_once "type_donnees.php";
            ?>
        </select>
        <label for="idValeurBonus<?php echo $idBonus; ?>">Valeur : </label>
        <input type="text" id ="idValeurBonus<?php echo $idBonus; ?>" class="chiffre" style="width:50px;"/>
        <select id="idUniteCompteur<?php echo $idBonus; ?>"  style="width:100px;">
            <option value = ""> </option>
            <?php
            //require_once "type_donnees.php";
            ?>
        </select>
    </div>
</div>