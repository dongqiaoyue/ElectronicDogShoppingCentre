<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>建议详情</title>
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
            <label class="col-sm-3 control-label">建议ID：</label>
            <div class="input-group col-sm-7">
                <input id="id" type="text" class="form-control" name="id" required="" aria-required="true" readonly="readonly" value="<?= $info['id'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系人：</label>
            <div class="input-group col-sm-7">
                <input id="name" type="text" class="form-control" name="name" required="" aria-required="true" readonly="readonly" value="<?= $info['name'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系方式：</label>
            <div class="input-group col-sm-7">
                <input id="phone" type="text" class="form-control" name="phone" readonly="readonly" value="<?= $info['phone']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系人地址：</label>
            <div class="input-group col-sm-7">
                <input id="content" type="text" class="form-control" name="content" required="" readonly="readonly" aria-required="true" value="<?= $info['address'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">投诉内容：</label>
            <div class="input-group col-sm-7">
                <input id="content" type="text" class="form-control" name="content" required="" readonly="readonly" aria-required="true" value="<?= $info['content'] ?>">
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
    layui.use(['form','layer'], function(){

        //----------模块----------
        var form = layui.form;
        var layer = layui.layer;

        layer.photos({
            //类选择器  选择图片的父容器
            photos: '.layer-photos-demo'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });

    });
</script>
</body>
</html>