<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加管理员</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">

</head>
<body class="gray-bg">
<div class="ibox-content">
    <form class="form-horizontal m-t" method="post" action="/admins/add" id="commentForm">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <div class="form-group">
            <label class="col-sm-3 control-label">管理员名称：</label>
            <div class="input-group col-sm-7">
                <input id="admin_name" type="text" class="form-control" name="admin_name" required="" aria-required="true">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">管理员密码：</label>
            <div class="input-group col-sm-7">
                <input id="password" type="text" class="form-control" name="password" required="" aria-required="true">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">真实姓名：</label>
            <div class="input-group col-sm-7">
                <input id="real_name" type="text" class="form-control" name="real_name" required="" aria-required="true">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">管理员角色：</label>
            <div class="input-group col-sm-7">
                <select class="form-control" name="role_id" required="" aria-required="true">
                    <option value="">请选择</option>
                    <?php foreach($roles as $key => $vo): ?>
                        <option value="<?= $vo['role_id']; ?>"><?= $vo['role_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">是否启用：</label>
            <div class="input-group col-sm-7">
                <select class="form-control" name="status" required="" aria-required="true" id="status">
                    <option value="1">是</option>
                    <option value="2">否</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-9">
                <button class="btn btn-primary" type="submit">确定提交</button>
            </div>
        </div>
    </form>
</div>
<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/js/content.min.js?v=1.0.0"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/jquery.form.js"></script>
<script type="text/javascript">

    var index = '';
    function showStart(){
        index = layer.load(0, {shade: false});
        return true;
    }

    function showSuccess(res){

        layer.close(index);
        if(1 == res.code){

            layer.msg(res.msg);
            window.parent.initTable();
            setTimeout(function(){
                var p = parent.layer.getFrameIndex(window.name);
                parent.layer.close(p);
            }, 1000);

        }else if(111 == res.code){
            window.location.reload();
        }else{
            layer.msg(res.msg, {anim: 6});
        }

    }

    $(document).ready(function(){
        var options = {
            beforeSubmit: showStart,
            success: showSuccess
        };

        $('#commentForm').submit(function(){
            $(this).ajaxSubmit(options);
            return false;
        });
    });

</script>

</body>
</html>