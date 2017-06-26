<?php
require_once "fn_security.php";
check_session();
require_once "conn/connection.php";
require_once "accueil.php";
?>
<center><h3 class='htitre'>Répartition du parc par profil et par état</h3></center>
<?php
$parcs = $profils = array();
$status_arr = array(0 => 'IDLE', 1 => 'ACTIVE', 2 => 'SUSPENDED', 3 => 'DISABLE', 4 => 'POOL', 7 => 'POOL');
$req_prf = "SELECT code, libelle FROM ref_liste_choix_attribut where attribut = 'profil'";
$result = $connection->query($req_prf);
$ret = $result->fetchAll(PDO::FETCH_OBJ);
foreach ($ret as $li) {
    $profils[$li->code] = $li->libelle;
}

$req_seg = "SELECT profil, status, count(*) nbr FROM data_attribut WHERE status != -1 GROUP BY profil, status";
$result = $connection->query($req_seg);
$ret = $result->fetchAll(PDO::FETCH_OBJ);

foreach ($ret as $li) {
    $status = isset($status_arr[$li->status]) ? $status_arr[$li->status] : $li->status;
    $profil = isset($profils[$li->profil]) ? $profils[$li->profil] : $li->profil;
    $profil = ($profil == '') ? 'Sans profil' : $profil;
    $parcs[$profil][$status] = $li->nbr;
    if ($status == 'ACTIVE' || $status == 'SUSPENDED')
        $parcs[$profil]['total'] = (isset($parcs[$profil]['total'])) ? $parcs[$profil]['total'] + $parcs[$profil][$status] : $parcs[$profil][$status];
}
?>
<table id = 'dataTable' class=" dataTable display parc_profil" cellspacing="0" width="90%">
    <thead>
        <tr align ="right"><th align ="left">Profil</th><th>Total(A+S)</th><th>ACTIVE</th><th>SUSPENDED</th><th>DISABLE</th><th>POOL</th><th>IDLE</th></tr>
    </thead>
    <tbody>
        <?php
        $parc_total = array('TOTAL' => 0, 'IDLE' => 0, 'ACTIVE' => 0, 'SUSPENDED' => 0, 'DISABLE' => 0, 'POOL' => 0);
        foreach ($parcs as $profil => $tbCount) {
            $total = isset($tbCount['total']) ? $tbCount['total'] : 0;
            $idle = isset($tbCount['IDLE']) ? $tbCount['IDLE'] : 0;
            $active = isset($tbCount['ACTIVE']) ? $tbCount['ACTIVE'] : 0;
            $suspended = isset($tbCount['SUSPENDED']) ? $tbCount['SUSPENDED'] : 0;
            $disable = isset($tbCount['DISABLE']) ? $tbCount['DISABLE'] : 0;
            $pool = isset($tbCount['POOL']) ? $tbCount['POOL'] : 0;
            foreach ($parc_total as $key => $val) {
                $parc_total[$key] = $val + ${strtolower($key)};
            }
            echo "<tr>
                    <td>" . $profil . "</td>
                    <td align=right>" . $total . "</td>
                    <td align=right>" . $active . "</td>
                    <td align=right>" . $suspended . "</td>
                    <td align=right>" . $disable . "</td>
                    <td align=right>" . $pool . "</td>
                    <td align=right>" . $idle . "</td>
            </tr>";
        }
        echo "<tr>
                    <td> Total parc </td>
                    <td align=right>" . $parc_total['TOTAL'] . "</td>
                    <td align=right>" . $parc_total['ACTIVE'] . "</td>
                    <td align=right>" . $parc_total['SUSPENDED'] . "</td>
                    <td align=right>" . $parc_total['DISABLE'] . "</td>
                    <td align=right>" . $parc_total['POOL'] . "</td>
                    <td align=right>" . $parc_total['IDLE'] . "</td>
            </tr>"
        ?>
    </tbody>

</table>
<script>
    fnDataTable(1, "desc", "profil");
</script>