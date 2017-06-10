<?php
function getChirt($series,$option=array())
{
    extract($option);
    
    if($type == 'pie')
    {
        $series = array($series);
        $pointFormat = "pointFormat: '{series.name}: <b>{point.y}</b><br/>占比: <b>{point.percentage:.1f}%</b>',";
    }
    $series = json_encode($series);
    
    if($theme) 
    {
        $theme = "<script src='http://cdn.hcharts.cn/highcharts/themes/".$theme.".js'></script>";
    }
    if(!$type) $type = 'spline';
    if(!$width) $width = '100%';
    if(!$height) $height = '400px';
    if(!$ymin) $ymin = 0;

    if($category){
        $category = json_encode($category);
        $xstep = 1;
        $xmin = 0;
        $xtickstep = 1;
    }else{
        $category = '[]';
        if(!$xstep) $xstep=1;
        if(!$xmin) $xmin=0;
        if(!$xtickstep) $xtickstep = $xstep;
    }
    
    $html = "
<script type='text/javascript' src='http://cdn.hcharts.cn/jquery/jquery-1.8.3.min.js'></script>
<script src='http://cdn.hcharts.cn/highcharts/highcharts.js'></script>
<script src='http://cdn.hcharts.cn/highcharts/modules/exporting.js'></script>
".$theme."
<script type='text/javascript'>
$(function () {
    $('#container').highcharts({
        chart: {
            type: '".$type."'
        },
        title: {
            text: '".$title."',
//             x: -20,
        },
        subtitle: {
            text: '".$subtitle."'
        },
        xAxis: {
            categories: ".$category.",
            title: {
            	text: '".$xtitle."'
            },
    	    tickInterval: ".$xtickstep.",
            gridLineColor: '#333333',
            gridLineWidth: 0.5,
	        labels:{
//                 enabled: false,
            }
        },
        yAxis: {
            title: {
                text: '".$ytitle."'
            },
//             tickInterval: 4,
            gridLineColor:'#333333',
            gridLineWidth: 0.5,
            min:".$ymin.",
        },
        tooltip: {
            shared: true,
            valueSuffix: '".$suffix."',
            ".$pointFormat."
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.5,
                pointInterval:".$xstep.",
                pointStart: ".$xmin.",
            },
            line: {
//                 dataLabels: {
//                     enabled: true
//                 },
//                 enableMouseTracking: false
            },
            column: {
                dataLabels: {
                    enabled: true
                },
            },
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
//                 dataLabels: {
//                     enabled: false
//                 },
                showInLegend: true
            },
        },
        series: ".$series."
    });
});
</script>

<div id='container' style='width: ".$width."; height: ".$height."; margin: 0 auto'></div>
";
    return $html;
}
