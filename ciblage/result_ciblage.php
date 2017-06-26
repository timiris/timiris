<?php
require_once "../fn_security.php";
check_session();
$parc = (isset($_POST['parc'])) ? $_POST['parc'] : $parc;
$cible = (isset($_POST['cible'])) ? $_POST['cible'] : $cible;
?>
<script type="text/javascript">
    function pieChart() {
        var chart = new CanvasJS.Chart("chartContainer",
                {
                    zoomEnabled: true,
                    exportEnabled: true,
                    animationEnabled: true,
                    interactivityEnabled: true,
                    title: {
                        text: "Resultat ciblage sur un parc de : <?php echo number_format($parc); ?>"
                    },
                    animationEnabled: true,
                            legend: {
                        verticalAlign: "center",
                        horizontalAlign: "left",
                        fontSize: 20,
                        fontFamily: "Helvetica"
                    },
                    theme: "theme1",
                    data: [
                        {
                            type: "pie",
                            indexLabelFontFamily: "Garamond",
                            indexLabelFontSize: 20,
                            indexLabel: "{label} {y}",
                            startAngle: -20,
                            showInLegend: true,
                            toolTipContent: "{legendText} {y} client",
                            dataPoints: [
                                {y: <?php echo $parc - $cible; ?>, legendText: "Reste parc  <?php echo number_format(($parc - $cible) * 100 / $parc, 3, ',', ' '); ?>%", label: "Reste parc"},
                                {y: <?php echo $cible; ?>, legendText: "Cible  <?php echo number_format(($cible * 100 / $parc), 3, ',', ' '); ?>%", label: "Cible"}
                            ]
                        }
                    ]
                });
        chart.render();
    }
</script>

<div id="chartContainer" style="height: 300px; width: 100%;"><script>pieChart();
    $('a.canvasjs-chart-credit').html("TIMIRIS").attr('href', 'http://business-telecoms.com');</script></div>