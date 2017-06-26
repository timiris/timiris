<?php
require_once "../../fn_security.php";
check_session();
$dtj = date("Ymd");
$logFile_msc = "/tim_log/log_chargement/msc/import_msc_$dtj.log"; // local path to log file
$dir_msc_file = "/tim_DATA/cdrs/chargement/msc/";
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
    $cmd = ($_GET['type'] == 'msc') ? 'exec tail -n100 ' . $_GET['logName'] : 'date;exec ls -ltr ' . $_GET['logName'].' | tail -100 | grep ^"-"';

    if ($_GET['fc']) {
        echo shell_exec($cmd);
        $dt_access = shell_exec('stat -c%X ' . $_GET['logName']);
        $_SESSION['access_' . $_GET['type']] = $dt_access;
    } elseif ($dt_modiff > $_SESSION['access_' . $_GET['type']]) {
        echo shell_exec($cmd);
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
                width:500px;
                height:700px;
            }
            .div_log{
                position: relative;
                background-color: black;
                color: white;
                overflow-y: scroll;
                height:680px;
                font-family: 'Ubuntu', sans-serif;
                font-size: 12px;
                line-height: 12px;
                border-radius: 15px;
                padding-left:10px;
            }
            #div_msc {
                top:-30px;
                left:0px;
            }
            #div_ftp_msc {
                top:-30px;
                left:600px;
            }
        </style>
        <script>
            var firstCall = 1;
            tm_out_data = setInterval(readLogFileMSC, 1000);
            tm_out_ftp = setInterval(readREPMSC, 1000);
            var pathname = 'admin/supervision/logMSC.php';
            function readLogFileMSC() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $logFile_msc; ?>", fc: firstCall, type: 'msc'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_msc").html(data);
                        $('#log_msc').scrollTop($('#log_msc')[0].scrollHeight);
                    }
                });
            }
            function readREPMSC() {
                $.get(pathname, {getLog: "true", logName: "<?php echo $dir_msc_file; ?>", fc: firstCall, type: 'ftp'}, function(data) {
                    if (data.trim() != '-1') {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log_ftp_msc").html(data);
                        $('#log_ftp_msc').scrollTop($('#log_ftp_msc')[0].scrollHeight);
                    }
                });
            }
            readLogFileMSC();
            readREPMSC();
            firstCall = 0;
        </script>
        <body>
            <fieldset id ="div_msc" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_msc" class="leg_log">Log Chargement MSC</legend>
                <div id="log_msc" class="div_log"></div>
            </fieldset>
            <fieldset id ="div_ftp_msc" class="divGroupeCritere div_cont_log"  style = "border-radius:25px;">
                <legend name="log_ftp_msc" class="leg_log">FTP MSC</legend>
                <div id="log_ftp_msc" class="div_log"></div>
            </fieldset>
        </body>
    </html>
<?php } ?>
