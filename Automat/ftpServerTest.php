<?php

try {
    $ftp_server = '192.168.21.22';
    $ftp_user_name = 'timiris';
    $ftp_user_pass = '4525302217877';
    $conn_id = ftp_connect($ftp_server);
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
    if ((!$conn_id) || (!$login_result)) {
        die("Echec de la connexion FTP !");
    }
    ftp_pasv($conn_id, true);
    function getFiles($conn_id) {
        $rep_depo = '/home/timiris/ServerTest/';
        $rep_dist = '/home/timiris/ServerTest/';
        if (ftp_chdir($conn_id, $rep_dist)) {
            $buff = ftp_nlist($conn_id, ".");
            foreach ($buff as $v) {
                $localFile = $rep_depo . $v;
                echo "\n".$v ." : ".$localFile;
                if (!ftp_get($conn_id, $localFile, $v, FTP_BINARY, 0))
                    throw("Problème transfert de fichier ftp $v");
            }
        } else {
            echo "Impossible de changer le dossier\n".$rep_dist;
        }
    }

    getFiles($conn_id);
    ftp_close($conn_id);
} catch (Exception $e) {
    print_r($e);
}
echo "\r\nFin ftp " . date('Y-m-d H:i:s');
?>