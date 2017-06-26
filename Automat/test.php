<?php
$a = array(1, 5, 3) ;
$b = array(1, 5, 3) ;
print_r(add_arrays($a, $b));
function add_arrays($a, $b) {
    foreach ($b as $k => $v) {
        if (isset($a[$k]))
            $a[$k] += $v ;
        else
            $a[$k] = $v ;
    }
    return $a;
}

?>