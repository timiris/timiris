<?php

if (!isset($_SESSION))
    session_start();
if(!isset($rep))
$rep = '../../';
require_once $rep."fn_security.php";
check_session();
require_once $rep.'conn/connection.php';
$arr_profiles = array();
$rq = $connection->query("SELECT * FROM sys_profil");
while($pr = $rq->fetch(PDO::FETCH_OBJ))
        $arr_profiles[$pr->id] =$pr->nom;
//print_r($arr_profiles);
echo "<h2 align = 'center'>Liste WorkFlow de validation des campagnes</h2>";
echo '<div style = "margin:auto; width : 80%; padding: 10px;">';
echo "<table class ='dataTable display'>";
echo "<thead><tr><th>Initiateur</th><th>Etapes de validations</th></tr></thead><tbody>";
$rq = $connection->query('select * from ref_wf');
try {
    $i = 0;
    while ($ligne = $rq->fetch(PDO::FETCH_OBJ)) {
        $cls = ($i % 2) ? 'odd' : 'even';
        $i++;
        $idProfil = $ligne->profil;
        echo "<tr class=$cls><td>" . $arr_profiles[$idProfil] . "</td><td style ='white-space: nowrap;'>";
        $wf = json_decode($ligne->wf, true);
        echo $arr_profiles[$idProfil];
        foreach($wf as $fr =>$to)
            echo ' => '.$arr_profiles[$to];
        echo "</td></tr>";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
echo "</tbody></table></div>";
?>
