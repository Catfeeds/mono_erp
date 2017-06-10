<?php
header("Content-type:text/html;charset=utf-8");
error_reporting(0);
include_once 'Highcharts.php';

//条形图(colume)、折线图(line)、面积图(area) 数据格式
$series = array(
    array(
        'data'  =>  array(113, 114, 113, 115, 114, 120, 122),
        'name'  =>  'John',
    ),
    array(
        'data'  =>  array(111, 113, 114, 113, 113, 115, 114),
        'name'  =>  'Jane',
    ),
    array(
        'data'  =>  array(118, 112, 114, 119, 123, 120, 116),
        'name'  =>  'Lee',
    ),
);
//饼状图 数据格式
// $series = array(
//     'name' => '数量',//数据名称
//     'data' => array(
//         array(
//             'name' => 'Microsoft Internet Explorer',
//             'y' => 3,
//         ),
//         array(
//             'name' => 'Chrome',
//             'y' => 65,
//         ),
//         array(
//             'name' => 'Firefox',
//             'y' => 45,
//         ),
//         array(
//             'name' => 'Safari',
//             'y' => 47,
//         ),
//         array(
//             'name' => 'Opera',
//             'y' => 91,
//         ),
//         array(
//             'name' => 'Proprietary or Undetectable',
//             'y' => 102,
//         ),
//     ),
// );

$option = array(
    'type' => 'column',//图表类型 可选用: 折线line 曲线spline 柱状column 面积图area 曲线面积图areaspline 饼状pie

    'width' => '100%',//图表宽度,默认100%
    'height' => '400px',//图表宽度，默认400px
    'theme' => 'gray',//主题 可选用: dark-blue dark-green dark-unica gray grid-light grid sand-signika skies
    
    'title' => 'Myfarm',//图表标题
    'subtitle' => 'lolipop',//图表副标题
    'xtitle' => 'Date',//x轴名称
    'ytitle' => 'Fruit',//y轴名称
    'suffix' => ' kg',//数值单位

    'category' => array('Monday','Tuesday','Wednesday','Thursday',
        'Friday','Saturday','Sunday'),//固定的 x轴坐标
    'xstep' => 1,//线性的x轴坐标 步长 默认为1
    'xmin' => 0,//线性的x轴坐标 起始  默认为0  与category之间选用其中一种
    
    'ymin' => 110,//y轴初始值
);

$html = getChirt($series,$option);
echo $html;