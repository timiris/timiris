<?php
$options = "";
$req = "SELECT * FROM ref_categorie_operation WHERE code='" . $categorie . "' ";
try {
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $options .= "<option value = '" . $ligne->operateur . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>