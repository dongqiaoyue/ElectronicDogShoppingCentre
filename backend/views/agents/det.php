<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>代理商详情</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">

</head>
<body class="gray-bg">
<div class="ibox-content">
    <form class="form-horizontal m-t" id="commentForm">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <div class="form-group">
            <label class="col-sm-3 control-label">ID：</label>
            <div class="input-group col-sm-7">
                <input id="id" type="text" class="form-control" name="id" required="" aria-required="true" readonly="readonly" value="<?= $info['id'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">公司名称：</label>
            <div class="input-group col-sm-7">
                <input id="name" type="text" class="form-control" name="name" required="" aria-required="true" readonly="readonly" value="<?= $info['name'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系人：</label>
            <div class="input-group col-sm-7">
                <input id="contactName" type="text" class="form-control" name="contactName" readonly="readonly" value="<?= $info['contactName']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系方式：</label>
            <div class="input-group col-sm-7">
                <input id="contactPhone" type="text" class="form-control" name="contactPhone" readonly="readonly" value="<?= $info['contactPhone']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">相关资质图片：</label>
            <div class="input-group col-sm-7">
                <div  class="layer-photos-demo">
                <!--                <img src="--><?//= \yii\helpers\Url::to("@web".$info['image'])?><!--" alt="加载失败" height="150" width="150">-->
                <?php
                $images = $info['images'];
                $imagesArray = explode(",",$images);
                foreach ($imagesArray as $key => $value){
                    //echo '<img src="' . \yii\helpers\Url::to("@web".$value) . '"onclick="show_img(this)"  alt="加载失败" height="150" width="150">';
                    echo '<img layer-src="' . \yii\helpers\Url::to("@web".$value) . '" src="' . \yii\helpers\Url::to("@web".$value) . '"  alt="加载失败" height="150" width="150">';
                }
                ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">所在地区：</label>
            <div class="input-group col-sm-7">
                <input id="region" type="text" class="form-control" name="region" readonly="readonly" value="<?= $info['region']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">收货地址：</label>
            <div class="input-group col-sm-7">
                <input id="addr" type="text" class="form-control" name="addr" readonly="readonly" value="<?= $info['addr']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">订单备注：</label>
            <div class="input-group col-sm-7">
                <input id="memo" type="text" class="form-control" name="memo" required="" readonly="readonly" aria-required="true" value="<?= $info['memo'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">状态：</label>
            <div class="input-group col-sm-7">
                <input id="status" type="text" class="form-control" name="status" required="" readonly="readonly" aria-required="true" value="<?= $info['status'] ?>">
            </div>
        </div>
    </form>
</div>
<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/js/content.min.js?v=1.0.0"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/jquery.form.js"></script>
<script src="/static/js/layui/layui.js"></script>
<script>
    //显示大图片
    function show_img(t) {
        //console.log($(t).attr('src'));
        //页面层
        layer.open({
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['80%', '90%'], //宽高
            shadeClose: true, //开启遮罩关闭
            end: function (index, layero) {
                return false;
            },
            content: '<div style="text-align:center"><img src="' + $(t).attr('src') + '" /></div>'
        });
    }
</script>
<script>
    layui.use(['form','layer'], function(){

        //----------模块----------
        var form = layui.form;
        var layer = layui.layer;

//调用示例
        layer.photos({
            //类选择器  选择图片的父容器
            photos: '.layer-photos-demo'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });

    });
</script>
</body>
</html>