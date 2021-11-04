<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加节点</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">

</head>
<body class="gray-bg">
<div class="ibox-content">
    <form class="form-horizontal m-t" method="post" action="/nodes/add" id="commentForm">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <div class="form-group">
            <label class="col-sm-3 control-label">节点名称：</label>
            <div class="input-group col-sm-7">
                <input id="Name" type="text" class="form-control" name="Name" required="" aria-required="true">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">所属节点：</label>
            <div class="input-group col-sm-7">
                <select class="form-control" name="parentID" required="" aria-required="true">
                    <option value="0">顶级节点</option>
                    <?php foreach($nodes as $key => $vo): ?>
                        <option value="<?= $vo['ID']; ?>"><?= $vo['Name']; ?></option>
                        <?php if(!empty($vo['children'])): ?>
                            <?php foreach($vo['children'] as $k => $v): ?>
                                <option value="<?= $v['ID']; ?>">&nbsp;&nbsp;&nbsp; |-- <?= $v['Name']; ?></option>
                            <?php  endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">权限规则：</label>
            <div class="input-group col-sm-7">
                <input id="auth_rule" type="text" class="form-control" name="auth_rule" placeholder="全小写">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">是否是菜单项：</label>
            <div class="input-group col-sm-7">
                <select class="form-control" name="is_menu" required="" aria-required="true" id="is_menu">
                    <option value="1">否</option>
                    <option value="2">是</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">菜单图标：</label>
            <div class="input-group col-sm-7">
                <input id="style" type="text" class="form-control" name="style" placeholder="只有顶级节点需要">
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