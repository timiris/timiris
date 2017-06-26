<?php
if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
require_once $rep . "lib/tbLibelle.php";
$idGroup = $idCritere = 100;
if (isset($_POST["idGroup"]) and !empty($_POST["idGroup"]) and isset($_POST["idCritere"]) and !empty($_POST["idCritere"])) {
    $idGroup = $_POST["idGroup"];
    $idCritere = $_POST["idCritere"];
}

$nature = $_POST["nat_tr"];
$tp_dn = $_POST["tp_dn"];
$idDOM = $idGroup . '_' . $idCritere;
$table = $nature . '_' . $tp_dn;
$titreCritere = "{Nature Trafic : <b>" . $_POST["nat_tr_txt"] . "</b>}/{Type données : <b>" . $_POST["tp_dn_txt"] . "</b>}";
?>

<div id="critere_<?php echo $idDOM; ?>" class = "divCritere" style = "border :1px solid blue; margin-top:5px; border-radius:15px;">
    <span class = 'entCritere'><?php echo $titreCritere; ?></span>
    <span class="SupprimerDIV" name = "critere_<?php echo $idDOM; ?>" title="Supprimer le critére"></span>
    <span class="reduireDIV reduire" name = "critere_<?php echo $idDOM; ?>" title="Réduire">
        <img src = "img/moins.png" name = "img_reduire_critere_<?php echo $idDOM; ?>" width = "16" height = "16"/>
    </span>
    <div name = "divContent_critere_<?php echo $idDOM; ?>">
        <input type="hidden" id="natureType_<?php echo $idDOM; ?>" class="critere" value = "<?php echo $table; ?>"/>
        <?php
        require 'critereEvent.php';
        ?>
    </div>
</div>