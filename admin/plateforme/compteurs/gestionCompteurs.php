<?php
if(!isset($rep))
    $rep = '../../../';
if (!isset($_SESSION))
    session_start();
require_once $rep . "fn_security.php";
check_session();
require_once $rep . "conn/connection.php";
require_once $rep . "defs.php";
echo "<center><h2 class='htitre'>GESTION DES COMPTEURS & SERVICES</h2></center>";
?>
<div class="sky-tabs sky-tabs-pos-left sky-tabs-anim-flip" style="margin:auto; width:80%;">
    <input type="radio" name="sky-tabs" checked="" id="sky-tab1" class="sky-tab-content-1">
    <label for="sky-tab1"><span><span style='border:1px solid black;white-space: nowrap;'><i class="fa fa-bolt"></i>MONNAIE</span></span></label>
    <input type="radio" name="sky-tabs" id="sky-tab2" class="sky-tab-content-2">
    <label for="sky-tab2"><span><span style='border:1px solid black;white-space: nowrap;margin-top: -5px;'><i class="fa fa-picture-o"></i>TIME</span></span></label>
    <input type="radio" name="sky-tabs" id="sky-tab3" class="sky-tab-content-3">
    <label for="sky-tab3"><span><span style='border:1px solid black;white-space: nowrap;margin-top: -5px;'><i class="fa fa-cogs"></i>DATA</span></span></label>
    <input type="radio" name="sky-tabs" id="sky-tab4" class="sky-tab-content-4">
    <label for="sky-tab4"><span><span style='border:1px solid black;white-space: nowrap;margin-top: -5px;'><i class="fa fa-cogs"></i>SMS</span></span></label>
    <input type="radio" name="sky-tabs" id="sky-tab5" class="sky-tab-content-5">
    <label for="sky-tab5"><span><span style='border:1px solid black;white-space: nowrap;margin-top: -5px;'><i class="fa fa-cogs"></i>SERVICE</span></span></label>
    <input type="radio" name="sky-tabs" id="sky-tab6" class="sky-tab-content-6">
    <label for="sky-tab6"><span><span style='border:1px solid black;white-space: nowrap;margin-top: -5px;'><i class="fa fa-cogs"></i>FIDELITE</span></span></label>
    <label for="sky-tab7"><span><span style='border:1px solid black;white-space: nowrap;'><i class="fa fa-bolt"></i>TIMIRIS</span></span></label>
    <input type="radio" name="sky-tabs" id="sky-tab7" class="sky-tab-content-7">
    <ul class="vo">
        <li class="vo sky-tab-content-1">					
            <div class="typography" style="min-height: 200px;">
                <?php require_once 'monnaie.php'; ?>               
            </div>
        </li>
        <li class="vo sky-tab-content-2">
            <div class="typography" style="min-height: 200px;">
                <?php require_once 'time.php'; ?>
            </div>
        </li>
        <li class="vo sky-tab-content-3">
            <div class="typography" style="min-height: 200px;">
                <?php require_once 'data.php'; ?>
            </div>
        </li>	
        <li class="vo sky-tab-content-4">
            <div class="typography" style="min-height: 200px;">
                <?php require_once 'sms.php'; ?>
            </div>
        </li>	
        <li class="vo sky-tab-content-5">
            <div class="typography" style="min-height: 200px;">
                <?php require_once 'service.php'; ?>
            </div>
        </li>	
        <li class="vo sky-tab-content-6">
            <div class="typography" style="min-height: 200px;">
                <?php require_once 'fidelite.php'; ?>
            </div>
        </li>	
        <li class="vo sky-tab-content-7">
            <div class="typography" style="min-height: 200px;">
                <?php require_once 'timiris.php'; ?>
            </div>
        </li>	
    </ul>
</div>