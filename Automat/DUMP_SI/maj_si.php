<?php

try {
    require_once '../../conn/connection.php';
    $rep = '/tim_DATA/dump/si/zip/';
    $tbFiles = scandir($rep);
    unset($tbFiles[0]);
    unset($tbFiles[1]);
    foreach ($tbFiles as $file) {
        if (substr($file, -4) != '.TXT')
            continue;
        $fileContent = file($rep . $file);
        echo "Début du fichier $file " . date('His') . "\n\r";
        foreach ($fileContent as $li) {
            $exp = explode('|', $li);
            $msisdn = '222' . $exp[0];
            $nni = substr(preg_replace('/[^0-9+]/', "", $exp[1]), 0, 16);
            $birth = substr(preg_replace('/[^0-9+]/', "", $exp[5]), 0, 8);
            $birth = substr($birth, -4) . '-' . substr($birth, 2, 2) . '-' . substr($birth, 0, 2);
            $name = trim($exp[3]);
            $prenom = trim($exp[4]);
            if ($name != $prenom)
                $name = $name . ' ' . $prenom;
            $name = preg_replace("/[^A-Za-zèéêç'\-]/", ' ', $name);
            $name = str_replace('  ', ' ', $name);
            $req = "update data_attribut set nni = '$nni', date_naissance ='$birth', nom = '" . str_replace("'", "''", $name) . "' WHERE numero = '$msisdn'";
            try {
                $connection->query($req);
            } catch (Exception $e) {
                echo $li."\n\r";
                continue;
            }
        }
        echo "Fin du fichier $file " . date('His') . "\n\r";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
    echo $req;
}
?>