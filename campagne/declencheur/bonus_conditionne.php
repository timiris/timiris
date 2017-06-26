<?php
if (!isset($rep))
    $rep = '../../';
if (!isset($_SESSION))
    session_start();
require_once $rep . "fn_security.php";
check_session();
?>

<div id = "cntGrEvent">
    <fieldset class="section" style = "border-radius:15px; background-color: #ddd;">
        <legend>Régle d'association des groupes</legend>
        <input id="AssocGroupDeclench"  name="AssocGroupDeclench" type="radio" value = "or" checked>
        <label for="AssocGroupDeclench">Au moins un groupe</label>
    </fieldset>

    <?php
    $cls = "";
    if ($type == 'evenement') {
        $cls = 'event';
    }
    require_once "groupe.php";
//    else
//        require_once $rep . "ciblage/groupe.php";
    ?>
</div>
<div>
    <div style = "display: inline-block; position:absolute; left:20px">
        <button  class="button12 black declencheur <?php echo $cls; ?> AjouterGroupe" style="display: inline-block;">+ Groupe</button>
    </div>
</div>
<br/><br/>
<div id = "cntBonus">
    <?php
    $_POST["idBonus"] = 'bnsgeneral';
    require '../bonus/bonus_groupe.php';
    ?>
</div>