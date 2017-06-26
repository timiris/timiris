<?php
if (!isset($rep))
    $rep = '../';
require_once $rep . "fn_security.php";
check_session();
$dis = (isset($_POST['enrg'])) ? ' disabled' : '';
?>
<div id = "cntGrCiblage">
    <fieldset id="idFieldSetGroup" class="section" style = "border-radius:15px; background-color: #ddd;">
        <legend>Régle d'association des groupes</legend>
        <input id="AssocGroupAnd" name="associationGroupe" type="radio" checked="checked" value = "and">
        <label for="AssocGroupAnd">Tous les groupes</label>

        <input id="AssocGroupOr"  name="associationGroupe" type="radio" value = "or">
        <label for="AssocGroupOr">Au moins un groupe</label>
    </fieldset>
    <?php
    require_once "groupe.php";
    ?>
</div>
<div>
    <div style = "display: inline-block; position:absolute; left:20px">
        <button class="button12 black AjouterGroupe" style="display: inline-block;">+ Groupe</button>
    </div>
    <div style = "position :absolute; display: inline-block; right:10px;" >
        <button id="idCalculerCible" class="button12 blue" style="display: inline;">Calculer Cible</button>
        <?php
        if (!isset($_POST['enrg']))
            echo ' <button id="enregistrerCiblage" class="button12 blue" style="display: inline;" >Enregistrer</button>';
        ?>
        <form action = "ciblage/calcule_cible.php" method = "POST" id="formExporterCiblage" style ="display:inline">
            <input type = "hidden" id = "idInputParms" name = "parms">
            <input type="submit" id="exportCiblage" class="button12 blue" style="display: inline;" value ="Exporter">
        </form>
    </div>
</div>
<br/><br/><br/>
<div id= "resultatCiblage">
</div>
