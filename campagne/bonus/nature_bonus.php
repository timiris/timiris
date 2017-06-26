<?php

$retour = array();
$retour['exec'] = 0;
try {
    if (!isset($rep))
        $rep = "../../";
    require_once $rep . "fn_security.php";
    check_session();
    $retour['nature'] = '';
    $retour['cmp'] = '<option value = ""> </option>';
    $retour['unite'] = '<option value = "p">%</option>';
    if (isset($_POST["typeBonus"]) and !empty($_POST["typeBonus"])) {
        $idType = $_POST["typeBonus"];
        require_once $rep . 'conn/connection.php';
        if ($idType == 1) {   // Bonus libre
            $retour['nature'] = '<option value = ""> </option>';
            $req = "SELECT * FROM ref_nature_bonus WHERE etat = 1 order by id desc";
            $result = $connection->query($req);
            if ($result->rowCount()) {
                while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                    $retour['nature'] .= "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                }
            }
            $retour['unite'] = '<option value = ""> </option>';
        } else {  // Bonus proportionnel, on doit charger que les types qui colle avec la nature
            $idNature = (int) $_POST["idNatureTrafic"];
            require_once 'config.php';
            $retour['nature'] = '<option value = ""> </option>';
            $req = "SELECT * FROM ref_nature_bonus WHERE etat = 1 and id in (" . implode(',', array_keys($conf_bonus_pp[$idNature])) . ") order by id desc";
            $result = $connection->query($req);
            if ($result->rowCount()) {
                while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                    $retour['nature'] .= "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
                }
            }
        }
        $retour['exec'] = 1;
    }
} catch (PDOException $e) {
    $retour["message"] = $e->getMessage();
}
echo json_encode($retour);
?>