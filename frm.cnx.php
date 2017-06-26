<div class="container frm-cnx">
    <div id ="logoBTelecom">
        <img src = "img/btelecom.png">
        <br/><br/>
    </div>
    <section id="contentFrm">
        <form action="" method = "POST">
            <h1 class="frm-cnx"><?php echo $message; ?></h1>
            <div class="frm-cnx">
                <input type="text" class="frm-cnx" placeholder="Username" required="" id="username" name="username" autofocus value = "<?php echo $username; ?>" />
            </div>
            <div class="frm-cnx">
                <input type="password" class="frm-cnx" placeholder="Password" required="" id="password" name="password" value = "<?php echo $password; ?>"/>
            </div>
            <div class="frm-cnx">
                <input type="submit" class="frm-cnx" value="Se connecter" />
            </div>
        </form>
    </section>
</div>
