<?php

function formatter_date($dt) {
    if (strlen($dt) == 14)
        return substr($dt, 0, 4) . '-' . substr($dt, 4, 2) . '-' . substr($dt, 6, 2) . ' ' . substr($dt, 8, 2) . ':' . substr($dt, 10, 2) . ':' . substr($dt, 12, 2);
    elseif (strlen($dt) == 8)
        return substr($dt, 0, 4) . '-' . substr($dt, 4, 2) . '-' . substr($dt, 6, 2);
    
    return $dt;
}

?>