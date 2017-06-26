<?php

function getDateRel($dt, $pr) {
    switch ($pr) {
        case 'j' :
            $dateJ = date_create(date("Y-m-d"));
            $dt = date_create($dt);
            $diff = date_diff($dt, $dateJ)->days;
            break;
        case 'm' :
            $dateJ = date_create(date("Y-m-d"));
            $dt = date_create($dt . '-01');
            $diff = date_diff($dt, $dateJ)->m;
            break;
        case 'a' :
            $dateJ = date_create(date("Y-m-d"));
            $dt = date_create($dt . '-01-01');
            $diff = date_diff($dt, $dateJ)->y;
    }
    return $diff;
}

function getDateRelInv($num, $pr) {
    list($m, $d, $y) = explode('-', date('m-d-Y'));
    switch ($pr) {
        case 'j' :
            $diff = date('Y-m-d', mktime(0, 0, 0, $m, $d - $num, $y));
            break;
        case 'm' :
            $diff = date('Y-m', mktime(0, 0, 0, $m - $num, $d, $y));
            break;
        case 'a' :
            $diff = date('Y', mktime(0, 0, 0, $m, $d, $y - $num));
    }
    // echo '<br>'.$num .' : '.$pr.' : '.$diff.'<br>';
    return $diff;
}

?>