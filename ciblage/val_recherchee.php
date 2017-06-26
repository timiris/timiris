<?php
$divValRech = "";
if (strtolower($html) == 'input') {
    $max = ($maxlength != '') ? 'maxlength = "' . $maxlength . '"' : '';
    $divValRech = '<input type = "text" id = "valeurCritere_' . $_POST["id"] . '" ' . $max . ' size ="6" class = "critere ' . $class . '" />';
} elseif (strtolower($html) == 'select') {
    $req = "SELECT * FROM ref_liste_choix_attribut WHERE attribut = '$code'";
    $result = $connection->query($req);
    $divValRech = '<SELECT id = "valeurCritere_' . $_POST["id"] . '" class = "critere">';
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $divValRech .= "<option value = '" . $ligne->code . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
    }
    $divValRech .= "</SELECT>";
}
?>