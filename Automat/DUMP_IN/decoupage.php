<?php

$rep_chargement = '/tim_DATA/dump/in/';
$rep_arch = '/tim_arch/dump/in/';
$rep_depo_local = '/tim_DATA/dump/in/chargement/';

$tbFiles = scandir($rep_chargement);
unset($tbFiles[0]);
unset($tbFiles[1]);
$tbConsid = array();
$decoupage = false;
foreach ($tbFiles as $key => $fileName) {
    $spl = explode('_', $fileName);
    if (count($spl) != 3)
        continue;
    echo "Début traitement du fichier $fileName\n\r";
    $dt = $spl[0];
    $ocs = $spl[1];
    $rest = explode('.', $spl[2]);
    if (count($rest) < 2)
        continue;
    $sub = $rest[0];
    $ext = $rest[1];
    $cbp = '101';
    if (strlen($dt) == 8 && substr($dt, 0, 2) == 20 && $ocs == 'ocs' && $sub == 'subscriber' && $ext == 'list') {
        if (substr($fileName, -3) == '.gz') {
            $cmd = "gunzip $rep_chargement" . $fileName;
            exec($cmd);
            $fileName = str_replace('.gz', '', $fileName);
            echo "Le fichier $fileName est dézipé\n\r";
        }
        if (substr($fileName, -5) == '.list') {
            $tbConsid[] = $fileName;
            $cmd = "split -dl 50000 --additional-suffix=.list " . $rep_chargement . $fileName . " " . $rep_depo_local . "din" . $dt . "_" . $cbp . "_";
            exec($cmd);
            echo "Le fichier $fileName est splité\n\r";
            rename($rep_chargement . $fileName, $rep_arch . $fileName);
            echo "Le fichier $fileName est archivé\n\r";
        }
        $decoupage = true;
    }
}
if ($decoupage) {
    require_once '../connection.php';
    $connection->query('UPDATE data_attribut SET status = -1 WHERE status in (3,4)');
}
?>