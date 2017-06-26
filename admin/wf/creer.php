<?php

if (!isset($_POST['profil']))
    exit();
if (!isset($rep))
    $rep = '../../';
if (!isset($_SESSION))
    session_start();
require_once $rep . "fn_security.php";
check_session();
require_once $rep . "conn/connection.php";
$profil = (int) $_POST['profil'];
//echo '<span class="menu_admin" name ="admin/wf/list" style = "position:absolute; right:10px;">Liste WorkFlow</span>';
echo '<span class="menu_admin" name ="admin/profil/creer" style = "position:absolute; right:10px;">Liste profil</span>';
echo '<div style = "margin:auto; width : 50%; padding: 10px;">';
echo "<center><div style ='max-width: 800px;' align=center><div id = 'spanRetour' class='alert-box'></div>";
echo "<table class ='dataTable display' id='idTableAddWF'>";
echo "<thead><td colspan ='3'><h2 align = 'center'>Création WorkFlow de validation</h2></td></tr></thead>";

echo "<tbody><th class = 'even' align='right'>Profil initiataire </th><td>
    <select disabled name='0' class='wfv' style='width: 210px;'>";

$rq = $connection->query('SELECT * FROM sys_profil');
$allProfil = $printed = array();
$printed[] = $profil;
while ($pr = $rq->fetch(PDO::FETCH_OBJ)) {
    $allProfil[$pr->id] = $pr->nom;
    if ($profil == $pr->id)
        echo '<option value ="' . $pr->id . '" selected>' . $pr->nom . '</option>';
    else
        echo '<option value ="' . $pr->id . '">' . $pr->nom . '</option>';
}
echo '</select></td><td><img src="img/plus.png" class="addValidator" title="Ajouter Validataire"  style="cursor:pointer;"/></td></tr>';

$wf = $connection->query('select wf from ref_wf where profil =' . $profil);
if ($wf->rowCount()) {
    $i = 1;
    $wf = json_decode($wf->fetch(5)->wf, true);
    foreach ($wf as $v) {
        $opt = '';
        foreach ($allProfil as $k => $n)
            if (!in_array($k, $printed)) {
                if ($k == $v)
                    $opt .= '<option value="' . $k . '" selected>' . $n . '</option>';
                else
                    $opt .= '<option value="' . $k . '">' . $n . '</option>';
            }
        echo "<th align='right'>Validateur N° $i</th><td colspan='2'><select name='$i' class='wfv' style='width: 210px;'>$opt</select></td></tr>";
        $i++;
        $printed[] = $v;
    }
}
echo '</tbody></table><br>';
echo '<input class="button12 blue" style = "width:120px ;font-size : 100%; font-weight:bold;"  type = "button" id="idEnregistrerWF" value = "Enregistrer">';
?>
