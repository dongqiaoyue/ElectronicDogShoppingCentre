<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑代理商信息</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
    <script src="/static/js/layui/city-picker/assets/jquery-1.12.4.js"></script>
    <link rel="stylesheet" href="/static/js/layui/city-picker/layui/css/layui.css" />
    <script type="text/javascript" src="/static/js/layui/city-picker/layui/layui.js"></script>
    <script type="text/javascript" src="/static/js/layui/city-picker/assets/data.js"></script>
    <script type="text/javascript" src="/static/js/layui/city-picker/assets/province.js"></script>

</head>
<body class="gray-bg">
<div class="ibox-content">
    <form class="form-horizontal m-t" method="post" action="/agents/price" id="commentForm">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <input name="id" type="hidden" id="_csrf" value="<?= $id ?>">
        <div class="layui-form">
            <div class="layui-form-item" id="area-picker">
                <div class="col-sm-3 control-label">
                    <label >选择商品:</label>
                </div>
                <div class="layui-input-inline col-sm-7">
                    <select name="goodsID" id="goodsID" class="province-selector" data-value="" lay-filter="province-1">
                        <option value="">请选择</option>
                        <?php foreach ($goods as $vo){?>
                            <option value="<?=$vo->id?>"><?=$vo->title?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品价格：</label>
            <div class="input-group col-sm-7">
                <input id="price" type="text" class="form-control" name="price"  value="">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-9">
                <button class="btn btn-primary" type="submit">确定提交</button>
            </div>
        </div>
    </form>
</div>
<div class="form-group">
    <br>
    <br>

    <?php if($info != ''){foreach ($info as $vo){?>
        <label class="col-sm-3 control-label"><?=$vo['title']?></label>
        <div class="input-group col-sm-7">
            <input id="price" type="text" class="form-control" name="price"  value="<?=$vo['price']?>">
        </div>
    <?php }}?>
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
        console.log(res);
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
<script src="/static/js/layui/layui.js"></script>
<script>
    layui.use('form', function(){
        var form = layui.form;
    });
</script>
<script>
    layui.use('layedit', function(){
        var layedit = layui.layedit;
        //layedit.build('contents'); //建立编辑器

    });
</script>
</body>
</html>