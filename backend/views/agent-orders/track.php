<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单跟踪</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
    <legend>订单跟踪</legend>
</fieldset>
<ul class="layui-timeline">
    <?php
    foreach ($all as $key => $value){
        echo '<li class="layui-timeline-item">';
        echo '<i class="layui-icon layui-timeline-axis"></i>';
        echo '<div class="layui-timeline-content layui-text">';
        echo '<div class="layui-timeline-title">'.date("Y-m-d H:i:s",$value['time']).'</div>';
        echo '<p>';
        echo $value['content'];
        echo '</p>';
        echo '</div>';
        echo '</li>';
    }
    ?>
</ul>


<script src="/static/js/layui/layui.js"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
</script>

</body>
</html>
