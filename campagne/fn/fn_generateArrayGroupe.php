<?php

function fn_generateArrayGroupe($idGrp, $nature, $periode, $dt, $connection) {
    try {
        $tb = array();
        //$tb['associationCritere_1'] = 'and';
        $req = 'select * from app_campagne_declencheur where fk_id_groupe = ' . $idGrp;
        $res = $connection->query($req);
        $cr = 1;
        while ($dec = $res->fetch(PDO::FETCH_OBJ)) {
            $tb['natureType_1_' . $cr] = $nature . '_' . $dec->fk_id_td;
            $tb['idTypeCompteur_1_' . $cr] = strtolower($dec->code_declencheur);
            $tb['idUnitePeriodique_1_' . $cr] = $periode;
            $tb['idPeriodeFrom_1_' . $cr] = $dt;
            $tb['idPeriodeTo_1_' . $cr] = $dt;
            $tb['idFormule_1_' . $cr] = 'least';
            $tb['operateur_1_' . $cr] = $dec->operateur;
            $tb['valeurCritere_1_' . $cr] = $dec->valeur;
            $tb['untieValeur_1_' . $cr] = $dec->unite;
            $cr++;
        }
        return $tb;
    } catch (PDOException $e) {
        return ($e);
    }
}

?>