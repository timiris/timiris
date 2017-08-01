<?php

try {
    $rep = '../../';
//    require_once 'function_insert.php';
    require_once 'connection.php';
    require_once 'correspondance.php';
    require_once 'tbAllTables.php';
//    require_once 'tbCompteurModel.php';

    $config['pos'] = $NewPos;
    $config['val'] = $NewVal;
    $idx_bns = 0;
    $reqGlobal = '';

    echo $endOfLine . "Début parsing, process : '" . getmypid() . "'  " . date("Ymd H:i:s");
    $rep_chargementLocal = $rep_log . 'mechili/';
    $rep_sauv = $rep_chargementLocal . 'traite/';
    $tbFiles = scandir($rep_chargementLocal);
//    unset($tbFiles[0]);
//    unset($tbFiles[1]);
    foreach ($tbFiles as $k => $f)
        if (is_dir($rep_chargementLocal . $f))
            unset($tbFiles[$k]);
    rsort($tbFiles);
    $i = 0;
    $j = 0;
    $tbConsid = $tbFiles;
    $allRq = $cdr = $tbMSISDN = $allRqAttr = $nbrFile = $af = $arr_glb_bonus = array();
//****************************** DEB PARTIE TRAITEMENT *********************
    if (count($tbConsid)) {
//***************** CHARGEMENT DES GROUPES DE DECLENCHEMENT
        //require 'declencheur_generique.php';
//***************************************************
//echo $endOfLine."Chargement des fichiers , process : '" . getmypid() . "'";
        foreach ($tbConsid as $keyFile => $fileName) {
            if (!count($nbrFile))
                echo $endOfLine . "Chargement des fichiers , process : '" . getmypid() . "'";

            $nbrLigne = $nbrLigneCons = $nbrEligibility = 0;
            $fichier = $rep_chargementLocal . $fileName;
            echo $endOfLine . $fileName . ", H:" . date('His');
            $lg_max = $config['val']['max'];
            $fileContent = file($fichier);
            $nbLContent = count($fileContent);
            if ($nbLContent) {
                foreach ($fileContent as $li) {
                    $nbrLigne++;
                    $cdr = json_decode($li, true);
                    if (count($cdr)) {
                        GenRq($cdr, $tb_crspd, 1);
                        $nbrLigneCons++;
                    }
                }
                $nbrFile[] = $fileName;
            }
//****************************** FIN PARTIE TRAITEMENT *********************
// *************************** PARTIE MAJ **********************************
            if (count($nbrFile) == $config['nb_files'] || count($nbrFile) == count($tbConsid)) {
                try {
                    echo $endOfLine . "Début traitement, " . count($tbMSISDN) . " nums, process : '" . getmypid() . "' H:" . date('His');
                    // execute_attribut($tbMSISDN, $allRqAttr, $connection);
                    $connection->query('BEGIN');
                    execute_requete($tbMSISDN, $allRq, $connection);
                    //execute_all_bonus($arr_glb_bonus, $connection, $dtJour);
                    echo $endOfLine . "Fin traitement , process : '" . getmypid() . "' H:" . date('His');
//                    $reqGlobal = "INSERT INTO app_fichier_charge (fichier, dt_chargement, nbr_ligne, nbr_ligne_considere, type_fichier, dt_first, dt_last, dt_jour, dt_mois, cbp, seq)
//                       VALUES " . implode(', ', $af);
//                    $connection->query($reqGlobal);
                    if ($connection->query('COMMIT')) {
                        echo $endOfLine . "Archivage des fichiers , process : '" . getmypid() . "' ";
                        foreach ($nbrFile as $fileName) {
                            $fichier = $rep_chargementLocal . $fileName;
                            if (is_dir($rep_sauv) || mkdir($rep_sauv)) {
                                $nvEmpNom = $rep_sauv . $fileName;
                            } else
                                $nvEmpNom = $rep_sauv . $fileName;
                            $fs = rename($fichier, $nvEmpNom);
                            echo $endOfLine . $fileName . ", H:" . date('His');
                        }
                        $allRq = $cdr = $tbMSISDN = $arr_glb_bonus = $allRqAttr = $nbrFile = $af = array();
                    }
                } catch (Exception $e) {
                    $connection->query('ROLLBACK');
                    throw($e);
                }
            }
        } // for consider
// *************************** FIN PARTIE MAJ **********************************
    }
    echo $endOfLine . "Fin parsing, process : '" . getmypid() . "'  " . date("Ymd H:i:s");
} catch (Exception $e) {
    echo $endOfLine;
    echo($e->getMessage());
    echo $endOfLine;
}
$connection = null;
?>