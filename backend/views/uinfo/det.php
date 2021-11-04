<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户详情</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">

</head>
<body class="gray-bg">
<div class="ibox-content">
    <form class="form-horizontal m-t" id="commentForm">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <div class="form-group">
            <label class="col-sm-3 control-label">用户ID：</label>
            <div class="input-group col-sm-7">
                <input id="id" type="text" class="form-control" name="id" required="" aria-required="true" readonly="readonly" value="<?= $info['id'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">电话号码：</label>
            <div class="input-group col-sm-7">
                <input id="phone" type="text" class="form-control" name="phone" readonly="readonly" value="<?= $info['phone']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">添加时间：</label>
            <div class="input-group col-sm-7">
                <input id="addAt" type="text" class="form-control" name="password" required="" readonly="readonly" aria-required="true" value="<?= $info['addAt'] ?>">
            </div>
        </div>
    </form>
</div>
<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/js/content.min.js?v=1.0.0"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/jquery.form.js"></script>

</body>
</html>