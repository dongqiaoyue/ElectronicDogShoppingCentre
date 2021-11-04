<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加代理商</title>
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
    <form class="form-horizontal m-t" method="post" action="/agents/add" id="commentForm">
        <div class="layui-input-item">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <div class="form-group">
            <label class="col-sm-3 control-label">公司名称：</label>
            <div class="input-group col-sm-7">
                <input id="name" type="text" class="form-control" name="name" required="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系人：</label>
            <div class="input-group col-sm-7">
                <input id="contactName" type="text" class="form-control" name="contactName" required="" aria-required="true">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系电话：</label>
            <div class="input-group col-sm-7">
                <input id="contactPhone" type="text" class="form-control" name="contactPhone" required="" aria-required="true">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">密码：</label>
            <div class="input-group col-sm-7">
                <input id="password" type="text" class="form-control" name="password" required="" aria-required="true">
            </div>
        </div>
<!--        <div class="form-group">-->
<!--            <label class="col-sm-3 control-label">地区：</label>-->
<!--            <div class="input-group col-sm-7">-->
<!--                <input id="region" type="text" class="form-control" name="region" required="" aria-required="true">-->
<!--            </div>-->
<!--        </div>-->
            <div class="layui-form">
                <div class="layui-form-item" id="area-picker">
                    <div class="col-sm-3 control-label">
                        <label >选择地区:</label>
                    </div>
                    <div class="layui-input-inline col-sm-7">
                        <select name="province" id="province" class="province-selector" lay-filter="province-1">
                            <option value="">请选择省</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="city" id="city" class="city-selector" lay-filter="city-1">
                            <option value="">请选择市</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="region" id="region" class="county-selector" lay-filter="county-1">
                            <option value="">请选择县/区</option>
                        </select>
                    </div>
                </div>
            </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">收货地址：</label>
            <div class="input-group col-sm-7">
                <input id="addr" type="text" class="form-control" name="addr" required="" aria-required="true">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">备注：</label>
            <div class="input-group col-sm-7">
                <input id="memo" type="text" class="form-control" name="memo" required="" >
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">是否启用：</label>
            <div class="input-group col-sm-7">
                <select class="form-control" name="status" required="" aria-required="true" id="status">
                    <option value="1">是</option>
                    <option value="0">否</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-9">
                <button  class="btn btn-primary" type="submit">确定提交</button>
<!--                <button id="test"  class="btn btn-primary" type="button">测试</button>-->
            </div>
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
        //console.log(index);
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
<script src="/static/js/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/css/area-picker/mods/'
        , version: '1.0'
    });
    layui.use(['layer', 'form', 'layarea'], function () {
        var layer = layui.layer
            , form = layui.form
            , layarea = layui.layarea;

        layarea.render({
            elem: '#area-picker',
            // data: {
            //     province: '广东省',
            //     city: '深圳市',
            //     county: '龙岗区',
            // },
            change: function (res) {
                //选择结果
                console.log(res);
            }
        });
    });
</script>
</body>
</html>