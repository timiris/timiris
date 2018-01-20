<?php

try {
    $rep = '../../';
//    require_once 'function_insert.php';
    $dtBasculement = 20180101000001;
    $seqBasculement = 48585;

    require_once 'connection.php';
    $reqVerif = "SELECT * FROM sys_cron WHERE type = '" . $config['cdrName'] . "' and etat = TRUE";
    $resVerif = $connection->query($reqVerif);
    if (!$resVerif->rowCount())
        exit();
    require_once 'correspondance.php';
    require_once 'tbAllTables.php';
//    require_once 'tbCompteurModel.php';

    $config['pos'] = $NewPos;
    $config['val'] = $NewVal;
    $idx_bns = 0;
    $reqGlobal = '';

    echo "\r\nDébut parsing, process : '" . getmypid() . "'  " . date("Ymd H:i:s");
    $rep_chargementLocal = $rep_chargement . strtolower($config['cdrName']) . '/';
    $tbFiles = scandir($rep_chargementLocal);
    unset($tbFiles[0]);
    unset($tbFiles[1]);
    rsort($tbFiles);
    $i = 0;
    $j = 0;
    $tbConsid = array();
    while ($j < $config['nbrFichierCharge'] && count($tbFiles)) {
        $nbrRqSend = $nbrCdrRead = $nbrFileUpp = 0;
        $tbConsid = array();
        while ($j < $config['nbrFichierCharge'] && isset($tbFiles[$i])) {
            if (strpos($tbFiles[$i], $config['cdrName'], 0) === 0 && substr($tbFiles[$i], -4) == '.unl') {
                $exp_file = explode('_', $tbFiles[$i]);
                if (count($exp_file) == 6 && ($exp_file[1] == "101" || $exp_file[1] == "102")) {
                    if ($exp_file[4] > $dtBasculement) {    //&& $exp_file[5] > $seqBasculement Controle nouveau et ancien flux
                        $tbConsid[] = $tbFiles[$i];
                        $j++;
                    }
                }
            }
            unset($tbFiles[$i]);
            $i++;
        }
    }
    $allRq = $cdr = $tbMSISDN = $allRqAttr = $nbrFile = $af = $arr_glb_bonus = array();
//****************************** DEB PARTIE TRAITEMENT *********************
    if (count($tbConsid)) {
        $strFileNames = "'" . implode("','", $tbConsid) . "'";
        $reqVerifFiles = 'SELECT fichier from app_fichier_charge where fichier in (' . $strFileNames . ')';
        $resVerifFiles = $connection->query($reqVerifFiles);
        $tbTrt = $resVerifFiles->fetchAll(PDO::FETCH_COLUMN, 0);
        $tbConsid = array_diff($tbConsid, $tbTrt);
//*************** déplacer les fichiers deja traités

        foreach ($tbTrt as $fileName) {
            $fichier = $rep_chargementLocal . $fileName;
            $nvEmpNom = $rep_doublons . $fileName;
            echo "\n\rDoublon : $fileName";
            $fs = rename($fichier, $nvEmpNom);
        }

//***************** CHARGEMENT DES GROUPES DE DECLENCHEMENT
        require 'declencheur_generique.php';
//***************************************************
//echo "\n\rChargement des fichiers , process : '" . getmypid() . "'";
        foreach ($tbConsid as $keyFile => $fileName) {
            if (!count($nbrFile))
                echo "\n\rChargement des fichiers , process : '" . getmypid() . "'";

            $dtFirst = date('YmdHis');
            $dtLast = '';
            $nbrLigne = $nbrLigneCons = $nbrEligibility = 0;
            $fichier = $rep_chargementLocal . $fileName;
            echo "\n\r" . $fileName . ", H:" . date('His');
            $lg_max = $config['val']['max'];
            $fileContent = file($fichier);
            $nbLContent = count($fileContent);
            if ($repGlobal != '')
                $nbLnLinux = $nbLContent;
            else
                $nbLnLinux = exec("cat $fichier | wc -l");
            if (!$nbLnLinux) { // reject the file
                echo "\r\nLe fichier : $fileName est vide";
                $nvEmpNom = $rep_file_rejected . $fileName;
                $fs = rename($fichier, $nvEmpNom);
                unset($tbConsid[$keyFile]);
//                $allRq = $cdr = $tbMSISDN = $allRqAttr = $nbrFile = $af = $arr_glb_bonus = array();
                $fileName = '';
            } elseif ($nbLContent != $nbLnLinux) {   //ignore the file
                echo "\r\nLe fichier : $fileName est sauté";
                unset($tbConsid[$keyFile]);
                $fileName = '';
            } else {
                foreach ($fileContent as $li) {
                    $nbrLigne++;
                    $cdr = array();
                    if (strlen($li > 10)) {
                        $ligne_explode = explode($config['separateur'], $li);
                        if (count($ligne_explode) >= $lg_max) {
                            foreach ($config['pos'] as $key => $val) {
                                $cdr[$key] = $ligne_explode[$val];
                            }
                            if (isset($config['pfx'])) {
                                foreach ($config['pfx'] as $key => $val) {
                                    $cdr[$key] = $val . $cdr[$key];
                                }
                            }
                            if ($cdr['heure'] < $dtFirst)
                                $dtFirst = $cdr['heure'];
                            if ($cdr['heure'] > $dtLast)
                                $dtLast = $cdr['heure'];
                            $RetourGenInfosCdr = GenInfosCdr($cdr, $config, $fileName);
                            if (isset($RetourGenInfosCdr['considere'])) {
                                GenRq($RetourGenInfosCdr, $tb_crspd);
                                if (count($grp_dec)) {
                                    $nbrEligibility++;
                                    $ret = fn_eligibility($RetourGenInfosCdr, $config['cdrName']);
                                    if (count($ret)) {
//                                        echo "\n\r" . $cdr['msisdn'] . "\n\rEligibility : " . json_encode($ret) . "\n\r";
                                        $ret = fn_calcul_bonus($RetourGenInfosCdr, $ret);
//                                        echo "Bonus calculés : " . json_encode($ret) . "\n\r";
                                    }
                                }
                                $nbrLigneCons++;
                            }
                        } else {//Ligne incomplete
                            echo "\r\n Le cdr numéro : $nbrLigne du fichier $fileName ne contient que " . count($ligne_explode) . ", alors que max est : $lg_max \r\n";
                            $nvEmpNom = $rep_file_rejected . $fileName;
                            $fs = rename($fichier, $nvEmpNom);
                            unset($tbConsid[$keyFile]);
                            echo "\r\nFichier cdr incomplet : $fileName";
                            $allRq = $cdr = $tbMSISDN = $allRqAttr = $nbrFile = $af = $arr_glb_bonus = array();
                            $fileName = '';
                            break;
                        }
                    }
                }
            }

            if ($fileName) {
                $nbrFile[] = $fileName;
                $ret_v = verif_init($tb_init, $config['cdrName'], $dtFirst, $dtLast);
                if (count($ret_v)) {
                    $subject = 'Alerte Initialisation' . $config['cdrName'];
                    $body = "Bonjour,<br>
                        Nous vous informons que le chargement des cdrs " . $config['cdrName'] . " s'est arrêter suite au problème d'initialisation mentionné par la suite.";
                    $altbody = "Bonjour,\r\n
                        Nous vous informons que le chargement des cdrs " . $config['cdrName'] . " s'est arrêter suite au problème d'initialisation mentionné par la suite.";
                    foreach ($ret_v as $k => $v) {
                        $body .= "<li>La date $k n'est pas encore initialisée !</li>";
                        $altbody .= "\r\n - La date $k n'est pas encore initialisée !";
                    }
                    $connection->query("update sys_cron set etat = false where type ='" . $config['cdrName'] . "'");
                    require_once 'mail/envoyer_mail.php';

                    $arr_cc = $arr_address = array();
                    $tbRetMail = sendMail($subject, $body, $arr_address, $arr_cc);
                    exit();
                }
                $inf_file = explode('_', $fileName);

                $dtMois = substr($inf_file[4], 0, 6);
                $dtJour = substr($inf_file[4], 0, 8);
                $cbp = (int) $inf_file[1];
                $seq = (int) str_replace('.unl', '', $inf_file[5]);

//                echo ", Nb.L: $nbrLigne, Nb.L.Cons: $nbrLigneCons";
                echo ",Nb.L.Cons: $nbrLigneCons /   $nbrLigne / $nbrEligibility";

                $af[] = "('$fileName', '" . date('YmdHis') . "', $nbrLigne, $nbrLigneCons, '" . $config['cdrName'] . "', '$dtFirst ', '$dtLast', '$dtJour', '$dtMois', $cbp, $seq)";
            }

//****************************** FIN PARTIE TRAITEMENT *********************
// *************************** PARTIE MAJ **********************************
            if (count($nbrFile) == $config['nb_files'] || count($nbrFile) == count($tbConsid)) {
                try {
                    echo "\n\rDébut traitement, " . count($tbMSISDN) . " nums, process : '" . getmypid() . "' H:" . date('His');
                    execute_attribut($tbMSISDN, $allRqAttr, $connection);
                    $connection->query('BEGIN');
                    execute_requete($tbMSISDN, $allRq, $connection);
                    execute_all_bonus($arr_glb_bonus, $connection, $dtJour);
                    echo "\n\rFin traitement , process : '" . getmypid() . "' H:" . date('His');
                    $reqGlobal = "INSERT INTO app_fichier_charge (fichier, dt_chargement, nbr_ligne, nbr_ligne_considere, type_fichier, dt_first, dt_last, dt_jour, dt_mois, cbp, seq)
                       VALUES " . implode(', ', $af);
                    $connection->query($reqGlobal);
                    if ($connection->query('COMMIT')) {
                        echo "\n\rArchivage des fichiers , process : '" . getmypid() . "' ";
                        foreach ($nbrFile as $fileName) {
                            $fichier = $rep_chargementLocal . $fileName;
                            $inf_file = explode('_', $fileName);
                            $dt = substr($inf_file[4], 0, 8);

                            if (is_dir($rep_sauv . $dt) || mkdir($rep_sauv . $dt)) {
                                $nvEmpNom = $rep_sauv . $dt . '/' . $fileName;
                            } else
                                $nvEmpNom = $rep_sauv . $fileName;
                            $fs = rename($fichier, $nvEmpNom);
                            echo "\n\r" . $fileName . ", H:" . date('His');
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
    echo "\r\nFin parsing, process : '" . getmypid() . "'  " . date("Ymd H:i:s");
} catch (Exception $e) {
    if ($config['nb_files'] == 1) {
        $nvEmpNom = $rep_file_error . $fileName;
        $fs = rename($fichier, $nvEmpNom);
        echo "\r\n";
        echo($e->getMessage());
        echo "\r\n";
    } else {
        $cmp_lim = array();
        $config['nbrFichierCharge'] = (int) ($config['nbrFichierCharge'] / 2) ? (int) ($config['nbrFichierCharge'] / 2) : 1;
        $config['nb_files'] = (int) ($config['nb_files'] / 2) ? (int) ($config['nb_files'] / 2) : 1;
        echo "\r\nBasculement mode " . $config['nb_files'] . " fichier par lot";
        include '../import_generique.php';
    }
}
$connection = null;
?>