<?php
require_once "../fn_security.php";
check_session();
$max = 0;
$tbDataGlobal = $tabData = array();
if ($graphMult) {
    $tb = '';
    if ($idBtn == 'icon_exp_excel') {
        header('Content-Type: application/x-msexcel');
        header('Content-disposition: attachment; filename=Timiris_' . date('YmdHis') . '.csv');
        $ligne = 0;
        $li_td = '';
        foreach ($tbVal as $cmp => $tbv) {
            ksort($tbv);
            array_pop($tbv);
            if (!$ligne)
                $li_th = ";";
            $li_td .= "\n" . $arr_lib_cmpt[strtolower($cmp)] . ';';
            foreach ($tbv as $th => $td) {
                if (!$ligne)
                    $li_th .= "$th;";
                $li_td .= number_format($td, 2, ',', ' ') . ';';
            }
            $ligne++;
        }
        echo $li_th . $li_td;
        exit();
    }

    echo '<div id="divStatsGlobal" class ="divShadow"></div>';
    echo '<script> var graphMult = true; </script>';

    foreach ($tbVal as $cmp => $tbv) {
        ksort($tbv);
        array_pop($tbv);
        $stats = $tbv;
        $tabData = array();
        foreach ($stats as $key => $val) {
            if ($val > $max)
                $max = $val;
            if (strlen($key) > 5) {
                $key = substr($key, -5);
                $arr_th = explode('-', $key);
                $key = $arr_th[1] . '-' . $arr_th[0];
            }
            $tabData[] = '{ label: "' . $key . '", y: ' . $val . '}';
        }
        $tbDataGlobal[$cmp] = '{ name :"' . $arr_lib_cmpt[strtolower($cmp)] . '", cursor: "pointer", type: "spline", showInLegend: true, legendText: "' . $arr_lib_cmpt[strtolower($cmp)] . '", dataPoints: [' . implode(', ', $tabData) . ']}';
    }
} else {
    ksort($tbVal);
    array_pop($tbVal);
    $stats = $tbVal;

    $tb = '';
    if ($idBtn == 'icon_exp_excel') {
        header('Content-Type: application/x-msexcel');
        header('Content-disposition: attachment; filename=Timiris_' . date('YmdHis') . '.csv');
        $li_th = "";
        $li_td = "\n";
        foreach ($tbVal as $th => $td) {
            $li_th .= "$th;";
            $li_td .= number_format($td, 2, ',', ' ') . ';';
        }
        echo $li_th . $li_td;
        exit();
    }
    echo '<script> var graphMult = false; </script>';
    foreach ($stats as $key => $val) {
        if ($val > $max)
            $max = $val;
        if (strlen($key) > 5) {
            $key = substr($key, -5);
            $arr_th = explode('-', $key);
            $key = $arr_th[1] . '-' . $arr_th[0];
        }
        $tabData[] = '{ label: "' . $key . '", y: ' . $val . '}';
    }
}
$interval = $max / 10;
?>
<script type="text/javascript">
    CanvasJS.addColorSet("customColorSet1",
            [//colorSet Array
                "#009FE0",
            ]);

    function Stats() {
        var chart = new CanvasJS.Chart("divStatsGlobal", {
            theme: "theme1", //theme1
            zoomEnabled: true,
            exportEnabled: true,
            animationEnabled: true,
            interactivityEnabled: true,
            colorSet: "customColorSet1",
            title: {
                text: "<?php echo $entete; ?>"
            },
            axisX: {
                labelAngle: 115,
                interval: 1
            },
            axisY: {
                title: "<?php echo $unite_lib; ?>",
            },
            data: [
                {
                    // Change type to "bar", "area", "spline", "pie",etc.
                    type: "column",
                    dataPoints: [
<?php echo implode(', ', $tabData); ?>
                    ]
                }
            ]
        });
        chart.render();
    }

    function statsMultiple() {
        var chart = new CanvasJS.Chart("divStatsGlobal",
                {
                    animationEnabled: true,
                    exportEnabled: true,
                    interactivityEnabled: true,
                    zoomEnabled: true,
                    toolTip: {
                        content: "{name}<br>{label} : {y}"
                    },
                    title: {
                        text: "<?php echo $entete; ?>"
                    }, axisX: {
                        labelAngle: 115,
                        interval: 1
                    },
                    axisY: {
                        title: "<?php echo $unite_lib; ?>",
                    },
                    data: [
<?php echo implode(', ', $tbDataGlobal); ?>
                    ],
                    legend: {
                        cursor: "pointer",
                        itemclick: function(e) {
                            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                                e.dataSeries.visible = false;
                            } else {
                                e.dataSeries.visible = true;
                            }
                            chart.render();
                        }
                    }
                });

        chart.render();
    }

    if (graphMult)
        statsMultiple();
    else
        Stats();
    $('a.canvasjs-chart-credit').html("TIMIRIS").attr('href', 'http://business-telecoms.com');
    $("#divStatsGlobal").attr("hight", "100%");
</script>