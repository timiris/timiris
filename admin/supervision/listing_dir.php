<?php
require_once "../../fn_security.php";
check_session();
$dir_bkp_bdd = "/tim_arch/backup_bdd/";
$dir_newEvent = "/tim_log/log_NewEvent/";
$dir_db_in = "/tim_DATA/cdrs/doublons/in/";
$dir_db_msc = "/tim_DATA/cdrs/doublons/msc/";
$dir_msc_error = "/tim_DATA/cdrs/error/msc/";
$dir_in_error = "/tim_DATA/cdrs/error/in/";
$dir_in_rej = "/tim_DATA/cdrs/rejected/in/";
$dir_msc_rej = "/tim_DATA/cdrs/rejected/msc/";

$interval = 1000; //how often it checks the log file for changes, min 100
$textColor = ""; //use CSS color
// Don't have to change anything bellow
if (!$textColor)
    $textColor = "white";
if ($interval < 100)
    $interval = 100;
if (isset($_GET['getLog'])) {
    // echo file_get_contents($_GET['logName']);
    $dt_modiff = shell_exec('stat -c%Y ' . $_GET['logName']);

    if ($_GET['fc']) {
        echo shell_exec('date;exec ls -ltr ' . $_GET['logName'].' | tail -100 | grep ^"-"');
        $dt_access = shell_exec('stat -c%X ' . $_GET['logName']);
        $_SESSION['access_' . $_GET['type']] = $dt_access;
    } elseif ($dt_modiff > $_SESSION['access_' . $_GET['type']]) {
        echo shell_exec('date; exec  ls -ltr ' . $_GET['logName'].' | tail -100 | grep ^"-"');
        $dt_access = shell_exec('stat -c%X ' . $_GET['logName']);
        $_SESSION['access_' . $_GET['type']] = $dt_access;
    } else {
        echo '-1';
    }
} else {
    ?>
    <html>
        <title>Log</title>
        <style>
            legend{
                font-size: 13px;
                line-height: 14px;
            }
            .div_cont_log{
                position: absolute;
                width:550px;
                height:200px;
            }
            .div_log{
                position: relative;
                background-color: black;
                color: white;
                overflow-y: scroll;
                height:160px;
                font-family: 'Ubuntu', sans-serif;
                font-size: 12px;
                line-height: 16px;
                border-radius: 15px;
                padding:10px;
            }
            #div_mgr {
                top:-30px;
                left:0px;
            }
            #div_sms {
                top:-30px;
                left:600px;
            }
            #div_rec {
                top:220px;
                left:0px;
            }
            #div_data {
                top:220px;
                left:600px;
            }
            #div_msc_error {
                top:470px;
                left:0px;
            }
            #div_in_error {
                top:470px;
                left:600px;
            }
            #div_msc_rej {
                top:720px;
                left:0px;
            }
            #div_in_rej {
                top:720px;
                left:600px;
            }
        </style>
        <script>
            var firstCall = 1;
            tm_out_rec = setInterval(readDirBkpBdd, 1000);
            tm_out_vou = setInterval(readDirNewEvent, 1000);
            tm_out_data = setInterval(readDirDbIN, 1000);
            tm_out_ftp = setInterval(readDirMSC, 1000);
            tm_out_sms = setInterval(readDirMSCError, 1000);
            tm_out_mgr = setInterval(readDirINError, 1000);
            tm_out_rej1 = setInterval(readDirINRej, 1000);
            tm_out_rej2 = setInterval(readDirMSCRej, 1000);
            var pathname = 'admin/supervision/listing_dir.php';
            function readDirBkpBdd() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_db_msc; ?>", fc: firstCall, type: 'rec'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_rec").html(data);
                        $('#log_rec').scrollTop($('#log_rec')[0].scrollHeight);
                    }
                });
            }
            function readDirNewEvent() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_newEvent; ?>", fc: firstCall, type: 'sms'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_sms").html(data);
                        $('#log_sms').scrollTop($('#log_sms')[0].scrollHeight);
                    }
                });
            }
            function readDirMSC() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_bkp_bdd; ?>", fc: firstCall, type: 'mgr'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_mgr").html(data);
                        $('#log_mgr').scrollTop($('#log_mgr')[0].scrollHeight);
                    }
                });
            }
            function readDirDbIN() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_db_in; ?>", fc: firstCall, type: 'data'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_data").html(data);
                        $('#log_data').scrollTop($('#log_data')[0].scrollHeight);
                    }
                });
            }
            function readDirMSCError() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_msc_error; ?>", fc: firstCall, type: 'data'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_msc_error").html(data);
                        $('#log_msc_error').scrollTop($('#log_msc_error')[0].scrollHeight);
                    }
                });
            }
            function readDirINError() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_in_error; ?>", fc: firstCall, type: 'data'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_in_error").html(data);
                        $('#log_in_error').scrollTop($('#log_in_error')[0].scrollHeight);
                    }
                });
            }
            function readDirMSCRej() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_msc_rej; ?>", fc: firstCall, type: 'data'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_msc_rej").html(data);
                        $('#log_msc_rej').scrollTop($('#log_msc_rej')[0].scrollHeight);
                    }
                });
            }
            function readDirINRej() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_in_rej; ?>", fc: firstCall, type: 'data'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_in_rej").html(data);
                        $('#log_in_rej').scrollTop($('#log_in_rej')[0].scrollHeight);
                    }
                });
            }
            readDirBkpBdd();
            readDirNewEvent();
            readDirDbIN();
            readDirMSC();
            readDirMSCError();
            readDirINError();
            readDirINRej();
            readDirMSCRej();
            firstCall = 0;

        </script>
        <body>
            <fieldset id ="div_rec" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>REP Doublons MSC</legend>
                <div id="log_rec" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_sms" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>REP NewEvent</legend>
                <div id="log_sms" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_mgr" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>REP Backup BDD</legend>
                <div id="log_mgr" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_data" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>REP Doublons IN</legend>
                <div id="log_data" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_msc_error" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>MSC CDRs ERROR</legend>
                <div id="log_msc_error" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_in_error" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>OCS CDRs ERROR</legend>
                <div id="log_in_error" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_msc_rej" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>MSC CDRs REJECTED</legend>
                <div id="log_msc_rej" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_in_rej" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend>OCS CDRs REJECTED</legend>
                <div id="log_in_rej" class="div_log"></div>
            </fieldset>
        </body>
    </html>
<?php } ?>