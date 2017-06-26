<?php
if (!isset($rep))
    $rep = '../../../';
if (!isset($_SESSION))
    session_start();
require_once $rep . "fn_security.php";
check_session();
?>
<div class="sky-tabs sky-tabs-pos-left sky-tabs-anim-flip" style="margin:auto; width:80%;">
    <input type="radio" name="sky-tabs" checked="" id="sky-tab1" class="sky-tab-content-1">
    <label for="sky-tab1"><span><span><i class="fa fa-bolt"></i>Chargement</span></span></label>
    <input type="radio" name="sky-tabs" id="sky-tab2" class="sky-tab-content-2">
    <label for="sky-tab2"><span><span><i class="fa fa-picture-o"></i>Bonus & SMS</span></span></label>
    <ul class="vo">
        <li class="vo sky-tab-content-1">
            <div class="typography" style="min-height: 200px;" >
                <ul class="uldb">
                    <?php require 'cron_cdr.php'; ?>
                </ul>
            </div>
        </li>
        <li class="vo sky-tab-content-2">
            <div class="typography" style="min-height: 200px;">
                <ul class="uldb">
                    <?php require 'cron_bns_sms.php'; ?>
                </ul>
            </div>
        </li>
    </ul>
</div>