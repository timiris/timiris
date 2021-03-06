<?php
session_start();
?>
<html>
    <head>
        <title>TIMIRIS</title>
        <meta charset="UTF-8">
        <?php
        require_once 'requiredCSS.conf';
        ?>
    </head>
    <body>
        <div id="header" style="display: block;">
            <table width ="100%">
                <tr>
                    <td><img src = "img/logoTimirisNew.png" id = "LogoTIMIRIS" height ="96"></td>
                    <td align="center" valign= "bottom"><h1 id = "TitreTIMIRIS">Gestion de fidélité</h1></td>
                    <td align="right"><img src = "img/MattelNew.png" id = "LogoMATTEL" height ="96"></td>
                </tr>
            </table>			
        </div>
        <?php
        $message = "Connexion";
        $s_user = (isset($_SESSION["user"]["id"]) && !empty($_SESSION["user"]["id"])) ? true : false;
        if (isset($_GET['message'])) {
            $message = $_GET['message'];
            $s_user = false;
        }

        if ($s_user) {
            require_once 'fn_formatter_date.php';
            require_once 'requiredJS.conf';
        }

        $username = $password = "";
        if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password'])) {

            $username = $_POST['username'];
            $password = $_POST['password'];
            $message = "<font color='red'>Incorrecte</font>";
            require_once 'conn/connection.php';
            $req = "SELECT * FROM sys_users WHERE login = '" . addslashes($_POST['username']) . "' AND etat = 1";
            try {
                $result = $connection->query($req);
                if ($result->rowCount()) {
                    $ligne = $result->fetch(PDO::FETCH_OBJ);
                    if ($ligne->pwd == 'timirisDefaultPassword') { // prémiére cnx, mettre a jour le mot de passe
                        $req_maj_pwd = "UPDATE sys_users SET pwd = '" . Md5(addslashes($_POST['password'])) . "' WHERE login = '" . addslashes($_POST['username']) . "'";
                        $result_maj_pwd = $connection->query($req_maj_pwd);
                    }

                    if ($ligne->pwd == Md5(addslashes($_POST['password'])) || $ligne->pwd == 'timirisDefaultPassword') { // connexion correcte
                        $req = 'select id_action from sys_actions_profil where id_profil = ' . $ligne->fk_id_profil;
                        $result = $connection->query($req);
                        $_SESSION["action"] = array();
                        while ($action = $result->fetch(PDO::FETCH_OBJ)) {
                            $_SESSION["action"][] = $action->id_action;
                        }

                        $_SESSION["user"] = array();
                        $_SESSION['lastActionTime'] = time();
                        $_SESSION["user"]["id"] = $ligne->id;
                        $_SESSION["user"]["profil"] = $ligne->fk_id_profil;
                        $_SESSION["user"]["entite"] = $ligne->fk_id_entite;
                        $_SESSION["user"]["nom"] = $ligne->nom;
                        $_SESSION["user"]["prenom"] = $ligne->prenom;
                        header('Status: 301 Moved Permanently', false, 301);
                        header('Location: .');
                        exit();
                    }
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        ?>
        <div id="conteneur" style="display: block;">
            <?php
            if ($s_user) {
                echo '<input type = "button" class = "button12 black" value = "Déconnexion" id = "idDeconnexion">';
                echo '<input type = "button" class = "button12 grey" value = "Modifier pwd" id = "idModPWD">';
            }
//                echo '<img src = "img/deconnecter.png" id = "idDeconnexion">';
            ?>

            <div id="content" style="display: block;">
                <div id="leftMenu">
                    <?php
                    if ($s_user)
                        require "menu/menu.php";
                    ?>
                </div>
                <div id="divPrincipale">
                    <?php
                    if ($s_user)
                        require $urlDefault . ".php";
                    else
                        require "frm.cnx.php";
                    ?>
                </div>
            </div>
            <?php
            if ($s_user) {
                ?>
                <div id = "dialog-message" title="TIMIRIS"></div>
                <div id = "popup" title="TIMIRIS"></div>
                <?php
            }
            ?>
        </div>
    </body>
</html>