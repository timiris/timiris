<?php

$ftp_server = '10.86.7.33';
$ftp_user_name = 'rbi';
$ftp_user_pass = 'rbi';
$rep_depo = '/tim_DATA/cdrs/chargement/in/';
$maps = array('rec' => 'rec', 'dat' => 'data', 'vou' => 'vou', 'mgr' => 'mgr', 'sms' => 'sms', 'mon' => 'mon', 'clr' => 'clr');
echo "\r\nDébut ftp " . date('Y-m-d H:i:s');
$conn_id = ftp_connect($ftp_server);
// Identification avec un nom d'utilisateur et un mot de passe
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// Véfication de la connexion
if ((!$conn_id) || (!$login_result)) {
    die("Echec de la connexion FTP !");
}
ftp_pasv($conn_id, true);
?>