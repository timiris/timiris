<?php

if (!isset($_SESSION))
    session_start();

function check_session() {
    $return = TRUE;
    if (!isset($_SESSION['user'])) {
        $return = false;
        $mes_ret = 'Session invalide, se déconnecter et connecter de nouveau';
    } else {
        $nwTime = time();
        if ($nwTime - $_SESSION['lastActionTime'] > 180000) {
            $mes_ret = 'Delai de la session est expiré, se déconnecter et connecter de nouveau';
            session_unset();
            session_destroy();
            $connection = null;
            $return = false;
        }
        else
            $_SESSION['lastActionTime'] = $nwTime;
    }

    if (!$return) {
        echo '<div class = "alert-box error">' . $mes_ret . '</div>';
        exit();
    }
}

?>
