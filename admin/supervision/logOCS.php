<?php
require_once "../../fn_security.php";
check_session();
$dtj = date("Ymd");
$logFile_rec = "/tim_log/log_chargement/rec/import_rec_$dtj.log"; // local path to log file
$logFile_data = "/tim_log/log_chargement/data/import_data_$dtj.log"; // local path to log file
$logFile_sms = "/tim_log/log_chargement/sms/import_sms_$dtj.log"; // local path to log file
$logFile_vou = "/tim_log/log_chargement/vou/import_vou_$dtj.log"; // local path to log file
$logFile_mgr = "/tim_log/log_chargement/mgr/import_mgr_$dtj.log"; // local path to log file
$logFile_ftp = "/tim_log/log_autre/ftpOCS_$dtj.log"; // local path to log file
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
        echo shell_exec('exec tail -n100 ' . $_GET['logName']);
        $dt_access = shell_exec('stat -c%X ' . $_GET['logName']);
        $_SESSION['access_' . $_GET['type']] = $dt_access;
    } elseif ($dt_modiff > $_SESSION['access_' . $_GET['type']]) {
        echo shell_exec('exec tail -n100 ' . $_GET['logName']);
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
                font-size: 14px;
                line-height: 14px;
            }
            .div_cont_log legend{
                cursor:pointer;
            }
            .div_cont_log{
                position: absolute;
                width:350px;
                height:400px;
            }
            .div_log{
                position: relative;
                background-color: black;
                color: white;
                overflow-y: scroll;
                height:380px;
                font-family: 'Ubuntu', sans-serif;
                font-size: 10px;
                line-height: 12px;
                border-radius: 15px;
                padding-left:10px;
            }
            #div_mgr {
                top:-30px;
                left:0px;
            }
            #div_rec {
                top:-30px;
                left:400px;
            }
            #div_sms {
                top:-30px;
                left:800px;
            }
            #div_data {
                top:400px;
                left:0px;
            }
            #div_vou {
                top:400px;
                left:400px;
            }
            #div_ftp {
                top:400px;
                left:800px;
            }
        </style>
        <script>
            var firstCall = 1;
            tm_out_rec = setInterval(readLogFileREC, 1000);
            tm_out_sms = setInterval(readLogFileSMS, 1000);
            tm_out_vou = setInterval(readLogFileVOU, 1000);
            tm_out_mgr = setInterval(readLogFileMGR, 1000);
            tm_out_data = setInterval(readLogFileDATA, 1000);
            tm_out_ftp = setInterval(readLogFileFTP, 1000);
            var pathname = 'admin/supervision/logOCS.php';
            function readLogFileREC() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $logFile_rec; ?>", fc: firstCall, type: 'rec'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_rec").html(data);
                        $('#log_rec').scrollTop($('#log_rec')[0].scrollHeight);
                    }
                });
            }
            function readLogFileFTP() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $logFile_ftp; ?>", fc: firstCall, type: 'ftp'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_ftp").html(data);
                        $('#log_ftp').scrollTop($('#log_ftp')[0].scrollHeight);
                    }
                });
            }
            function readLogFileDATA() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $logFile_data; ?>", fc: firstCall, type: 'data'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_data").html(data);
                        $('#log_data').scrollTop($('#log_data')[0].scrollHeight);
                    }
                });
            }
            function readLogFileVOU() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $logFile_vou; ?>", fc: firstCall, type: 'vou'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_vou").html(data);
                        $('#log_vou').scrollTop($('#log_vou')[0].scrollHeight);
                    }
                });
            }
            function readLogFileMGR() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $logFile_mgr; ?>", fc: firstCall, type: 'mgr'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_mgr").html(data);
                        $('#log_mgr').scrollTop($('#log_mgr')[0].scrollHeight);
                    }
                });
            }
            function readLogFileSMS() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $logFile_sms; ?>", fc: firstCall, type: 'sms'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_sms").html(data);
                        $('#log_sms').scrollTop($('#log_sms')[0].scrollHeight);
                    }
                });
            }

            readLogFileREC();
            readLogFileSMS();
            readLogFileMGR();
            readLogFileVOU();
            readLogFileDATA();
            readLogFileFTP();
            firstCall = 0;
        </script>
        <body>
            <fieldset id ="div_rec" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_rec" class="leg_log">Log Chargement REC</legend>
                <div id="log_rec" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_sms" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_sms" class="leg_log">Log Chargement SMS</legend>
                <div id="log_sms" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_mgr" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_mgr" class="leg_log">Log Chargement MGR</legend>
                <div id="log_mgr" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_data" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_data" class="leg_log">Log Chargement DATA</legend>
                <div id="log_data" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_vou" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_vou" class="leg_log">Log Chargement VOU</legend>
                <div id="log_vou" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_ftp" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_ftp" class="leg_log">Log FTP OCS</legend>
                <div id="log_ftp" class="div_log"></div>
            </fieldset>
        </body>
    </html>
<?php } ?>
