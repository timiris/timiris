<?php

try {
    require 'ftpNewOCS_conf.php';
    $rep_sauvegarde = '/tim_Arch/sauvegarde/in/';
    $lastFilesNewOCS = '/tim_DATA/cdrs/chargement/in/lastFilesNewOCS.unl';
    
    function getFiles() {
        global $conn_id;
            $rep = "/onip/mep/cdr/CBP/normal/bak/20170314/";
            if (ftp_chdir($conn_id, $rep)) {
                $buff2 = ftp_nlist($conn_id, "-t .");
                $i = 0;
                foreach ($buff2 as $v) {
                    echo ftp_mdtm($conn_id, $v)." : $v \n\r";
                    $i++;
                    if($i==20)
                        break;
                }
            } else {
                echo "Impossible de changer le dossier\n";
            }
    }
    
    getFiles();
    
    ftp_close($conn_id);
} catch (Exception $e) {
    print_r($e);
}
echo "\r\nFin ftp " . date('Y-m-d H:i:s');
?>