<?php
if (!isset($_SESSION))
    session_start();
require_once "../fn_security.php";
check_session();
?>
<!--
<div class="animatedtabs">
    <ul>
        <li id="b1" class="selected entTab"><p><span>Informations Générales</span></p></li>
        <li id="b2" class="entTab"><p><span>Ciblage</span></p></li>
        <li id="b3" class="entTab"><p><span>Attribution BONUS</span></p></li>
    </ul>
    <input type ="button" value="Enregistrer" id="idEnrgistrerCampagne"class="button12 blue" style="position:absolute;right: 15px"/>
</div>
-->

<div class="tabsChrome">
  <div id="b1" class="tabChrome active"><div class="tabChrome-box">Générales</div></div>
  <div id="b2" class="tabChrome"><div class="tabChrome-box">Ciblage</div></div>
  <div id="b3" class="tabChrome"><div class="tabChrome-box">BONUS</div></div>
  <div id="b4" class="tabChrome"><div class="tabChrome-box">BUDGET</div></div>
  <input type ="button" value="Enregistrer" id="idEnrgistrerCampagne"class="button12 blue" style="position:absolute;right: 15px"/>
</div>

<div id="contentTabs" class ="divShadow"> 
    <div id="tab1" class="divTab"><?php require "generales.php"; ?></div>
    <div id="tab2" class="divTab" style = "display:none;"><?php require "ciblage_campagne.php"; ?></div>
    <div id="tab3" class="divTab" style = "display:none;">
        <div id="div_declencheur">
            <?php
            require_once "declencheur/declencheur.php";
            ?>
        </div>
        <div id="div_bonus"></div>
    </div>
    <div id="tab4" class="divTab" style = "display:none;"><?php require "budget.php"; ?></div>
</div>