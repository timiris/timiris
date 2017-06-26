<?php

// Connection au serveur
try {
    date_default_timezone_set('Africa/Dakar');
    $connection = new PDO('pgsql:dbname=timiris;host=localhost;port=6432;','postgres','tim17877', 
            array(PDO::ATTR_TIMEOUT => 60) );
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "\n\rConnection à la base des donnés impossible pour le moment \n\r";
    die();
}
?>