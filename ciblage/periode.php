<?php

require_once "../fn_security.php";
check_session();
$rep = "../";
$idNature = 'j';
if (isset($_POST["idUnite"]) && !empty($_POST["idUnite"])) {
    $idUnite = $_POST["idUnite"];
}

$limit = $maxVal = 0;
if ($idUnite == 'j') {
    $limit = 31;
    $maxVal = date("Y-m-j");
}
if ($idUnite == 'm') {
    $limit = 12;
    $maxVal = date("Y-m");
}
if ($idUnite == 's') {
    $limit = 4;
    $maxVal = date("Y-m-j");
}
if ($idUnite == 'a') {
    $limit = 4;
    $maxVal = date("Y");
}

$min = $max = "";
require_once "../conn/connection.php";
$options = $idUnite . "|";
$optionsFrom = $optionsTo = "";
$req = "SELECT * FROM historique_correspondance WHERE type = '" . $idUnite . "' AND h_date <= '" . $maxVal . "' ORDER BY h_date LIMIT " . $limit;
// ORDER BY type ASC, poids DESC
try {
    $result = $connection->query($req);
    if ($result->rowCount()) {
        $nbRestant = $result->rowCount();
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            $nbRestant--;
            if ($min == "")
                $min = $ligne->h_date;
            $max = $ligne->h_date;
            $options .= "<option value = '" . $ligne->h_date . "'>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
            // $options .= "<option value = ".$ligne->champ.">".ucfirst(strtolower($ligne->h_date))."</option>";
            if ($optionsFrom == '')
                $optionsFrom = "<option value = '" . $ligne->h_date . "' selected>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
            else
                $optionsFrom .= "<option value = '" . $ligne->h_date . "'>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
            if ($nbRestant)
                $optionsTo .= "<option value = '" . $ligne->h_date . "'>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
            else
                $optionsTo .= "<option value = '" . $ligne->h_date . "' selected>" . ucfirst(strtolower($ligne->h_date)) . "</option>";
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
echo $idUnite . "|" . $optionsFrom . "|" . $min . "|" . $max . "|" . $optionsTo;
?>