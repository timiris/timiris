<?php
require_once "../../fn_security.php";
check_session();
$dtj = date("Ymd");
$log_ftp_din = "/tim_log/log_autre/ftpDIN.log"; // local path to log file
$log_parse_din = "/tim_log/log_chargement/din/import_dump_in_$dtj.log";
$dir_din = "/tim_arch/dump/in/";
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
    $cmd = ($_GET['type'] == 'log') ? 'tail -1000 ' . $_GET['logName'] : 'date;ls -ltr ' . $_GET['logName'].'*.* | tail -50 | grep ^"-" | awk \'{print $NF}\' ';
    if ($_GET['fc']) {
        echo str_replace($dir_din, '', shell_exec($cmd));
        $dt_access = shell_exec('stat -c%X ' . $_GET['logName']);
        $_SESSION['access_' . $_GET['type']] = $dt_access;
    } elseif ($dt_modiff > $_SESSION['access_' . $_GET['type']]) {
        echo str_replace($dir_din, '', shell_exec($cmd));
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
                height:500px;
            }
            .div_log{
                position: relative;
                background-color: black;
                color: white;
                overflow-y: scroll;
                height:480px;
                font-family: 'Ubuntu', sans-serif;
                font-size: 12px;
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
        </style>
        <script>
            var firstCall = 1;
            tm_out_data = setInterval(readLogDIN, 10000);
            tm_out_ftp = setInterval(readLogFtpDIN, 10000);
            tm_out_sms = setInterval(readRepDIN, 10000);
            var pathname = 'admin/supervision/logDUMP.php';
            function readLogDIN() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $log_parse_din; ?>", fc: firstCall, type: 'log'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_din").html(data);
                        $('#log_din').scrollTop($('#log_din')[0].scrollHeight);
                    }
                });
            }
            function readLogFtpDIN() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $log_ftp_din; ?>", fc: firstCall, type: 'log'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_ftp_din").html(data);
                        $('#log_ftp_din').scrollTop($('#log_ftp_din')[0].scrollHeight);
                    }
                });
            }
            function readRepDIN() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_din; ?>", fc: firstCall, type: 'dir'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_arch_din").html(data);
                        $('#log_arch_din').scrollTop($('#log_arch_din')[0].scrollHeight);
                    }
                });
            }
            readLogDIN();
            readLogFtpDIN();
            readRepDIN();
            firstCall = 0;
        </script>
        <body>
            <fieldset id ="div_mgr" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_din" class="leg_log">Log Chargement dump IN</legend>
                <div id="log_din" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_rec" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_ftp_din" class="leg_log">FTP dump IN</legend>
                <div id="log_ftp_din" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_sms" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_arch_din" class="leg_log">Archive dump IN</legend>
                <div id="log_arch_din" class="div_log"></div>
            </fieldset>
        </body>
    </html>
<?php } ?>
