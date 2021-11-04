<?php
use Hisune\EchartsPHP\ECharts;
?>
<div id="chart1">
    <?php
    //$asset=EchartsAsset::register($this);
    $chart = new ECharts();
    $day = date('Y-m-d', time());//获取当天日期
    $chart->title->text = '代理商各地区售货量统计图';//标题
    $chart->title->subtext = $date;//副标题
    $chart->title->left= 'center';//标题距离左侧的距离，这里设为居中
    $chart->tooltip->show = true;//提示框显示
    $chart->tooltip->trigger='axis';//数据项图形触发
    //$chart->legend->data = ['水位','降雨量'];//图例组件
    //$chart->legend->left= 'right';//图例组件显示在右边
    $chart->color = ['#d14a61','#3398DB'];//颜色就在这里定义，series会按顺序使用这些颜色。
    $chart->xAxis = array(
        'type' => 'category',
//        'data' =>  ['00:00','02:00','04:00','06:00','08:00','10:00',
//            '12:00', '14:00','16:00','18:00','20:00','22:00'],
        'data' => [$arrayProvince[0]['Name'],$arrayProvince[1]['Name'],$arrayProvince[2]['Name'],$arrayProvince[3]['Name'],$arrayProvince[4]['Name'],
            $arrayProvince[5]['Name'],$arrayProvince[6]['Name'],$arrayProvince[7]['Name'],$arrayProvince[8]['Name'],$arrayProvince[9]['Name'],
            $arrayProvince[10]['Name'],$arrayProvince[11]['Name'],$arrayProvince[12]['Name'],$arrayProvince[13]['Name'],$arrayProvince[14]['Name'],
            $arrayProvince[15]['Name'],$arrayProvince[16]['Name'],$arrayProvince[17]['Name'],$arrayProvince[18]['Name'],$arrayProvince[19]['Name'],
            $arrayProvince[20]['Name'],$arrayProvince[21]['Name'],$arrayProvince[22]['Name'],$arrayProvince[23]['Name'],$arrayProvince[24]['Name'],
            $arrayProvince[25]['Name'],$arrayProvince[26]['Name'],$arrayProvince[27]['Name'],$arrayProvince[28]['Name'],$arrayProvince[29]['Name'],
            $arrayProvince[30]['Name'],$arrayProvince[31]['Name']],
        'axisLabel' => [
                'interval' => 0,
//                'formatter' => function($value){
//                    $string ='';
//                    for($i = 0;$i < mb_strlen($value,'utf-8'); $i++){
//                        echo mb_substr($value,$i,1,'utf-8');
//                        echo "<br>";
//                        $string .= mb_substr($value,$i,1,'utf-8')."<br>";
//                    }
//                    return $string;
//                }
                'rotate' => '-45'
            ]

    );
    $chart->yAxis = array(
        array(
            'type' => 'value',
            'name' => '销售量',
            //'max' => '10000',
            'min'=>'0',
            'axisLine'=> [
                'lineStyle'=> [
                    'color'=> 'red'//定义Y轴颜色
                ]
            ],
            'axisLabel'=>[
                'formatter' =>'{value}个'//定义Y轴刻度标签
            ],
        ),
    );
    $chart->series = array(

        array(
                'name' => '销售量',
                'type' => 'line',
                'data' => [$arrayProvince[0]['count'],$arrayProvince[1]['count'],$arrayProvince[2]['count'],$arrayProvince[3]['count'],$arrayProvince[4]['count'],
                    $arrayProvince[5]['count'],$arrayProvince[6]['count'],$arrayProvince[7]['count'],$arrayProvince[8]['count'],$arrayProvince[9]['count'],
                    $arrayProvince[10]['count'],$arrayProvince[11]['count'],$arrayProvince[12]['count'],$arrayProvince[13]['count'],$arrayProvince[14]['count'],
                    $arrayProvince[15]['count'],$arrayProvince[16]['count'],$arrayProvince[17]['count'],$arrayProvince[18]['count'],$arrayProvince[19]['count'],
                    $arrayProvince[20]['count'],$arrayProvince[21]['count'],$arrayProvince[22]['count'],$arrayProvince[23]['count'],$arrayProvince[24]['count'],
                    $arrayProvince[25]['count'],$arrayProvince[26]['count'],$arrayProvince[27]['count'],$arrayProvince[28]['count'],$arrayProvince[29]['count'],
                    $arrayProvince[30]['count'],$arrayProvince[31]['count']],
        )
    );

    echo $chart->render('simple-custom-1');

    ?>
</div>