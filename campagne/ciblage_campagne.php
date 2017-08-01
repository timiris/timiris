<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
$chek1 = $chek2 = $chek3 = '';
if (isset($_POST['idCmp'])) {
    require_once "../defs.php";
    require_once "../conn/connection.php";
    require_once "../ciblage/fn/drawCible.php";
    require_once "../ciblage/fn/fn_getDateRel.php";
//    require_once "../lib/lib.php";
    require_once "../lib/tbLibelle.php";
    $idCmp = (int) $_POST['idCmp'];
    $cmp = $connection->query('SELECT * FROM app_campagne WHERE id = ' . $idCmp);
    if ($cmp->rowCount()) {
        $liCmp = $cmp->fetch(PDO::FETCH_OBJ);
        $idCC = (int) $liCmp->id_cible;
        if ($idCC > 0) {
            $cib = $connection->query('SELECT * FROM app_cibles WHERE id = ' . $idCC);
            $liCib = $cib->fetch(PDO::FETCH_OBJ);
            $nameCC = $liCib->nom;
            $CC = $liCib->cible;
            $assoc_group = $liCib->association_group;
            if ($nameCC == '')
                $chek2 = ' checked ';
            else
                $chek1 = ' checked ';
        }else {
            $chek3 = ' checked ';
        }
    }
} else {
    $chek1 = ' checked ';
}
?>
<div style="display:inline-block; width:33%;">
    <input type="radio" name = "ciblage_campagne_choix" id="idChosirCible" <?= $chek1; ?>><label for="idChosirCible">Choisir cible sauvegardée</label>
</div><div style='display:inline-block; width:33%;'>
    <input type="radio" name = "ciblage_campagne_choix" id="idComposerCible" <?= $chek2; ?>><label for="idComposerCible">Composer nouvelle cible</label>
</div><div style='display:inline-block; width:33%;'>
    <input type="radio" name = "ciblage_campagne_choix" id="idSansCible" <?= $chek3; ?>><label for="idSansCible">Tous le parc</label>
</div>
<hr>
<div id ="idDivCiblabeCampagne" style='font-size: 75%;'>
    <?php
    if ($chek1 != '') {
        require ('liste_cible.php');
        echo '<script>$("#listeCibleCampagne").change();</script>';
    } elseif ($chek3 != '')
        echo "<br><br><span class='alert-box success'>Tous le parc (actif + suspended)</span>";
    else {
        drawCible($idCC, $CC, $assoc_group, $connection, $idCmp);
    }
    ?>
</div>