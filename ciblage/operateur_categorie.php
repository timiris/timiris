<?php

if (!isset($rep))
    $rep = "../";
require_once $rep . "fn_security.php";
check_session();
if (isset($_POST["code"]) && !empty($_POST["code"])) {
    require_once $rep . "conn/connection.php";
    $unite = $options = "";
    $rfAttr = explode(":", $_POST["code"]);
    $code = $rfAttr[0];
    $categorie = $rfAttr[1];
    $html = $rfAttr[2];
    $maxlength = $rfAttr[3];
    $class = $rfAttr[4];
    $type = $rfAttr[5];
    $origine = (isset($rfAttr[6])) ? 'declencheur' : 'ciblage';
    $retour = array();
    // $divValRech = '<span>'.$ligne->libelle.' : </span>';
    $retour["sucess"] = 0;
    //******** unité
    require 'val_recherchee.php';
    require 'val_categorie_operation.php';
    // ORDER BY type ASC, poids DESC
  
    $retour["operator"] = $options;
    $retour["divValRech"] = $divValRech;
    $retour["sucess"] = 1;
    echo json_encode($retour);
}
?>