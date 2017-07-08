<?php

if (!isset($rep))
    $rep = '../../';

try {
    require_once '../connection.php';
    $reqVerif = "SELECT * FROM sys_cron WHERE type = '" . $config['cdrName'] . "' and etat = TRUE";
    $resVerif = $connection->query($reqVerif);
    if (!$resVerif->rowCount())
        exit();
    require_once '../correspondance.php';
    require_once 'fn.php';
    include('config.php');
    $FileCdrs = array();

    echo "\r\nDébut parsing, process : '" . getmypid() . "'  " . date("Ymd H:i:s");
    $tbFiles = scandir($rep_chargement);
    unset($tbFiles[0]);
    unset($tbFiles[1]);
    sort($tbFiles);
//    echo "\r\nNombre de fichier : ".count($tbFiles);
    $i = 0;
    $j = 0;
    while ($j < $config['nbrFichierCharge'] && count($tbFiles)) {
        $nbrRqSend = $nbrCdrRead = $nbrFileUpp = 0;
        $tbConsid = array();
        while ($j < $config['nbrFichierCharge'] && isset($tbFiles[$i])) {
            if (strpos($tbFiles[$i], $config['cdrName'], 0) === 0 && substr($tbFiles[$i], -4) == '.dat') {
                $tbConsid[] = $tbFiles[$i];
                $j++;
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
            $fichier = $rep_chargement . $fileName;
            $nvEmpNom = $rep_doublons . $fileName;
            echo "\r\nFichier : " . $fileName . " , doublons";
            $fs = rename($fichier, $nvEmpNom);
        }

        //***************** CHARGEMENT DES GROUPES DE DECLENCHEMENT
        require '../declencheur_generique.php';

        //***************************************************
        echo "\n\rChargement des fichiers , process : '" . getmypid() . "'";
        foreach ($tbConsid as $key => $fileName) {

            $dtFirst = date('YmdHis');
            $dtLast = '';
            $nbrLigne = $nbrLigneCons = 0;
            $fichier = $rep_chargement . $fileName;
            echo "\n\r" . $fileName . ", H:" . date('His');
            $fp = fopen($fichier, 'r');
            //*************** PARTIE TRAITEMENT FICHIER PAR FICHIER
            $content = fread($fp, filesize($fichier));
            $lnFile = '';
            $ln_lnFile = hexdec(hxp(1)) - 128;
            for ($i = 1; $i <= $ln_lnFile; $i++) {
                $lnFile .= hxp($i + 1);
            }
            $lnFile = hexdec($lnFile);
            $posLnFile = $ln_lnFile + 3;    // 3 positions a sauter (30 tag file, tag longueur file(82, 83) et le A0)
            $lnEntete = hexdec(hxp($posLnFile));
            $debFile = $posLnFile + $lnEntete + 1;  // Position du tag début file (A1)

            $lnContent = '';
            $ln_lnContent = hexdec(hxp($debFile + 1)) - 128;
            for ($i = 1; $i <= $ln_lnContent; $i++) {
                $lnContent .= hxp($i + $debFile + 1);
            }
            $lnContent = hexdec($lnContent);
            $posDebCdrs = $debFile + $ln_lnContent + 2;
            $posFinCdrs = $posDebCdrs + $lnContent;
            $debCdr = $posDebCdrs;
            $nbrLigne = $nbrLigneCons = 0;
            while ($debCdr < $lnContent /* && $NumCdr < 1000 */) {
                $lnTagCdr = $config[strtoupper(hxp($debCdr))];
                $lnCh_lnCdr = hexdec(hxp($debCdr + $lnTagCdr)) - 128;
                $strLong = '';
                for ($j = 1; $j <= $lnCh_lnCdr; $j++)
                    $strLong .= hxp($debCdr + $lnTagCdr + $j);
                $lnCdr = hexdec($strLong);
                $finCdr = $debCdr + $lnCh_lnCdr + $lnCdr + $lnTagCdr;
                $debContenuCdr = $debCdr + $lnCh_lnCdr + $lnTagCdr + 1;
                $tagDbCdr = hxp($debCdr);
                if ($tagDbCdr == 'A1' || $tagDbCdr == 'A7' /* || $tagDbCdr == 'A0' */) {
                    $rtParse = parse_cdr($debContenuCdr, $finCdr, $tagDbCdr);
                    if (count($rtParse)) {
                        if ($rtParse['heure'] < $dtFirst)
                            $dtFirst = $rtParse['heure'];
                        if ($rtParse['heure'] > $dtLast)
                            $dtLast = $rtParse['heure'];
                        $aDec = ${$config['tag_' . $tagDbCdr]};
                        $fnGenCdr = 'GenInfosCdr' . $config['tag_' . $tagDbCdr];
                        $rtGen = $fnGenCdr($rtParse);
                        if (isset($rtGen['considere'])) {
                            GenRq($rtGen, $tb_crspd, $tagDbCdr);
                            if (count($grp_dec)) {
                                $ret = fn_eligibility($rtGen, $tagDbCdr);
                                if (count($ret)) {
//                                    $fl = fopen($rep_log . $fileName, 'a');
//                                    if ($fl) {
//                                        fputs($fl, json_encode($ret) . " : ");
//                                        fputs($fl, json_encode($rtGen) . "\n\r");
//                                        fclose($fl);
//                                    }
                                    fn_calcul_bonus($rtGen, $ret);
                                }
                            }
                        }
                    }
                    $nbrLigneCons++;
                }
                $debCdr = $finCdr + 1;
                $nbrLigne++;
            }
            fclose($fp);
            $nbrFile[$fileName] = $dtFirst;
            //*************** FIN PARTIE TRAITEMENT FICHIER

            $ret_v = verif_init($tb_init, 'msc', $dtFirst, $dtLast);
            if (count($ret_v)) {
                $sujetMail = 'Alerte Initialisation';
                $body = "Bonjour,<br>
                        Nous vous informons que le chargement des cdrs " . $config['cdrName'] . " s'est arrêter suite au problème d'initialisation mentionné par la suite.";
                $altbody = "Bonjour,\r\n
                        Nous vous informons que le chargement des cdrs " . $config['cdrName'] . " s'est arrêter suite au problème d'initialisation mentionné par la suite.";
                foreach ($ret_v as $k => $v) {
                    $body .= "<li>La date $k n'est pas encore initialisée !</li>";
                    $altbody .= "\r\n - La date $k n'est pas encore initialisée !";
                }
                $connection->query("update sys_cron set etat = false where type ='". $config['cdrName'] ."'");
                require_once 'mail/envoyer_mail.php';
                exit();
            }
            $dtMois = substr($dtLast, 0, 6);
            $dtJour = substr($dtLast, 0, 8);
            $cbp = 1;
            $seq = (int) str_replace('.dat', '', str_replace('b', '', $fileName));
            echo ", Nb Ligne : $nbrLigne, Nb L. consid : $nbrLigneCons";
            $af[] = "('$fileName', '" . date('YmdHis') . "', $nbrLigne, $nbrLigneCons, '" . $config['cdrName'] . "', '$dtFirst ', '$dtLast', '$dtJour', '$dtMois', $cbp, $seq)";

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
                    $reqFile = "INSERT INTO app_fichier_charge (fichier, dt_chargement, nbr_ligne, nbr_ligne_considere, type_fichier, dt_first, dt_last, dt_jour, dt_mois, cbp, seq)
                       VALUES " . implode(', ', $af);
                    $connection->query($reqFile);
                    if ($connection->query('COMMIT')) {
                        echo "\n\rArchivage des fichiers , process : '" . getmypid() . "' ";
                        foreach ($nbrFile as $fileName => $dtFirst) {
                            $fichier = $rep_chargement . $fileName;
                            $dt = substr($dtLast, 0, 8);
                            if (is_dir($rep_sauv . $dt) || mkdir($rep_sauv . $dt)) {
                                $nvEmpNom = $rep_sauv . $dt . '/' . $fileName;
                            }
                            else
                                $nvEmpNom = $rep_sauv . $fileName;
                            $fs = rename($fichier, $nvEmpNom);
                            echo "\n\r" . $fileName . ", H:" . date('His');
                        }
                        if (count($nbrFile) != count($tbConsid))
                            echo "\n\rChargement des fichiers , process : '" . getmypid() . "'";
                        $allRq = $cdr = $tbMSISDN = $allRqAttr = $nbrFile = $af = $arr_glb_bonus = array();
                    }
                } catch (PDOException $e) {
                    $connection->query('ROLLBACK');
                    throw($e);
                }
            }
        } // for consider
        // *************************** FIN PARTIE MAJ **********************************
    }
    echo "\r\nFin parsing  " . date("Ymd H:i:s") . "\r\n";
} catch (PDOException $e) {
    if ($config['nb_files'] == 1) {
        $nvEmpNom = $rep_file_error . $fileName;
        $fs = rename($fichier, $nvEmpNom);
        echo($e->getMessage());
        echo "\r\n";
        print_r($e);
        echo "\r\n";
    } else {
        $config['nbrFichierCharge'] = (int) $config['nbrFichierCharge'] / 2 ? (int) $config['nbrFichierCharge'] / 2 : 1;
        $config['nb_files'] = (int) $config['nb_files'] / 2 ? (int) $config['nb_files'] / 2 : 1;
        echo "\r\nBasculement mode " . $config['nb_files'] . " fichier par lot";
        include 'import_msc.php';
    }
}
?>