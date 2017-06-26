<?php
$rep_chargement = "Chargment";
$rep_ignore = "Ignored";
$rep_considere = "Consid";
$Files = array();

function parcourir_dir($dir = '', $chemin = '') {
    global $Files;
    if (is_dir($dir)) {
        // echo $dir;
        $tbElts = scandir($dir);
        unset($tbElts[0]);
        unset($tbElts[1]);
        foreach ($tbElts as $elt) {
            $cheminFichier = $chemin . '/' . $elt;
            if (!is_dir($cheminFichier)) { // Est un fichier
                $ln = strlen($elt);
                if ($ln > 5) {
                    if (strtolower(substr($elt, $ln - 3, 3)) == 'jpg')
                        $Files[$chemin][$elt] = array('modif' => filemtime($chemin . '/' . $elt), 'OldName' => $chemin . '/' . $elt);
                }
                //echo 'est un fichier';
            }else { // est un répertoire	=> parcourir de nouveau
                //echo "<hr>";
                parcourir_dir($cheminFichier, $cheminFichier);
            }
        }
    }
}

parcourir_dir($rep_chargement, $rep_chargement);
// print_r($Files);
foreach ($Files as $chemin => $tb) {
    //echo "<hr>Répertoire $chemin <br>";
    $dtFirst = 0;
    foreach ($tb as $file => $infos) {
        $hour = (int) date('H', $infos['modif']);
        $newNameCosid = $rep_considere . '/' . $file;
        $newNameIgn = $rep_ignore . '/' . $file;
        if ($hour < 19 && $hour > 7) {
            if ($dtFirst == 0) {
                // echo 'Considérée '.$file . " ( date modification : ".$infos['modif']." ".date('Y-m-d H:i:s',$infos['modif']).", OldName : ".$infos['OldName'].")<br>";
                copy($infos['OldName'], $newNameCosid);
                $dtFirst = $infos['modif'];
            } else {
                if ($dtFirst + 1700 < $infos['modif']) {
                    // echo 'Considérée '. $file . " ( date modification : ".$infos['modif']." ".date('Y-m-d H:i:s',$infos['modif']).", OldName : ".$infos['OldName'].")<br>";
                    copy($infos['OldName'], $newNameCosid);
                    $dtFirst = $infos['modif'];
                }
                else
                // echo 'Non considérée '. $file . " ( date modification : ".$infos['modif']." ".date('Y-m-d H:i:s',$infos['modif']).", OldName : ".$infos['OldName'].")<br>";
                    copy($infos['OldName'], $newNameIgn);
            }
        }
        else
            copy($infos['OldName'], $newNameIgn);
    }
}
?>