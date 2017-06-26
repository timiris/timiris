<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
if (!isset($idCC))
    $idCC = -1;
?>
Liste cibles sauvegardées : 
<select id ="listeCibleCampagne">
    <option value = ''></option>
    <?php
    require_once "../conn/connection.php";
    $req = "SELECT id, nom FROM app_cibles ac WHERE ac.etat = 1 and ac.nom is not null";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        if ($idCC == $ligne->id)
            echo "<option value = '" . $ligne->id . "' selected>" . $ligne->nom . "</option>";
        else
            echo "<option value = '" . $ligne->id . "'>" . $ligne->nom . "</option>";
    }
    ?>
</select>
<br><br>
<div id='divShowCilbeCreationCampagne'></div>