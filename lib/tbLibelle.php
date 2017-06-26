<?php

if (!isset($rep))
    $rep = "../";
require_once $rep . "conn/connection.php";
$lib = array();
$lib['idTypeCompteur'] = 'Champ critère ';
$lib['valeurCritere'] = 'Valeur critère ';
$lib['operateur'] = 'Opérateur ';
$lib['idPeriodeFrom'] = 'Période ';
$lib['idPeriodeTo'] = ' au ';
$lib['idUnitePeriodique'] = 'Unité périodique ';
$lib['idFormule'] = ' Formule ';
$lib['untieValeur'] = '';
$lib['natureType'] = '{Nature Trafic : #_#_#}/{Type données : *_*_*}';

$tbOperateur = array();
$tbOperateur['like1'] = 'Commence par';
$tbOperateur['like2'] = 'Se termine par';
$tbOperateur['like3'] = 'Contient';
$tbOperateur['!='] = 'Différant de';
$tbOperateur['='] = 'Egal à';
$tbOperateur['<'] = 'Inferieur de';
$tbOperateur['<='] = 'Inferieur ou égal';
$tbOperateur['not in'] = 'N\'est pas parmis';
$tbOperateur['in'] = 'Parmis';
$tbOperateur['>'] = 'Supérieur à';
$tbOperateur['>='] = 'Supérieur ou égal';

$tbAssocGr = array();
$tbAssocGr['AND'] = 'tous les groupes';
$tbAssocGr['OR'] = 'au moins un groupe';


$tbFormule = array();
$tbFormule['least'] = 'Minimum';
$tbFormule['greatest'] = 'Maximum';
$tbFormule['SUM'] = 'Somme';
$tbFormule['AVG'] = 'Moyenne';

try {
    $libNature = array();
    $req = "SELECT * FROM  ref_nature";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libNature[$ligne->id] = $ligne->libelle;
    }

    $libRefEvent = array();
    $req = "SELECT * FROM  ref_event";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//		$libTypeDonnees[$ligne->id] = $ligne->libelle;
        $libRefEvent[$ligne->type][strtolower($ligne->code)] = $ligne->libelle;
    }
    
    $libTypeDonnees = array();
    $req = "SELECT * FROM  ref_type_donnee";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//		$libTypeDonnees[$ligne->id] = $ligne->libelle;
        $libTypeDonnees[$ligne->id] = array('libelle' => $ligne->libelle, 'unite' => json_decode($ligne->unite, true));
    }
    
    
    $libTypeDonneesEvent = array();
    $req = "SELECT * FROM  ref_type_donnee_event";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//		$libTypeDonnees[$ligne->id] = $ligne->libelle;
        $libTypeDonneesEvent[$ligne->id] = array('libelle' => $ligne->libelle, 'unite' => json_decode($ligne->unite, true));
    }
    
    $libNatBonus = array();
    $req = "SELECT * FROM  ref_nature_bonus";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libNatBonus[$ligne->id] = array('libelle' => $ligne->libelle, 'unite' => json_decode($ligne->unite, true));
    }
    
    $libTypeBonus = array();
    $req = "SELECT * FROM  ref_type_bonus";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libTypeBonus[$ligne->id] = $ligne->libelle;
    }

    $libCompteur = array();
    $req = "SELECT * FROM  ref_compteurs";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libCompteur[$ligne->fk_id_type . '_' . strtolower($ligne->code_cmpt)] = $ligne->libelle;
//		$libCompteur[$ligne->fk_id_type.'_'.strtolower($ligne->code_cmpt)] = array('libelle'=>$ligne->libelle, 'unite'=>json_decode($ligne->unite, true));
    }

    $libAttSelect = $libCritereAtt = array();
    $req = "SELECT * FROM  ref_attribut";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        if ($ligne->html == 'select')
            $libAttSelect[] = $ligne->code;
        $libCritereAtt[$ligne->code] = $ligne->libelle;
    }
    
    $libAttChoix = array();
    $req = "SELECT * FROM  ref_liste_choix_attribut";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libAttChoix[$ligne->attribut][$ligne->code] = $ligne->libelle;
    }
    
     $libEventSelect = array();
     $res = $connection->query("select code from ref_event where html = 'select'");
     while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
        $libEventSelect[] = $ligne->code;
    }

    $libCorrespondance = array();
    $req = "SELECT * FROM historique_correspondance ORDER BY h_date ";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libCorrespondance[$ligne->champ] = $ligne->h_date;
    }
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}
?>