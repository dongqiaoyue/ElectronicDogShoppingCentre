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
    <form class="form-horizontal m-t" method="post" action="/agents/edit" id="commentForm">
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
                <input id="name" type="text" class="form-control" name="name" required="" aria-required="true"  value="<?= $info['name'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系人：</label>
            <div class="input-group col-sm-7">
                <input id="contactName" type="text" class="form-control" name="contactName"  value="<?= $info['contactName']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">联系方式：</label>
            <div class="input-group col-sm-7">
                <input id="contactPhone" type="text" class="form-control" name="contactPhone"  value="<?= $info['contactPhone']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">相关资质图片：</label>
            <div class="input-group col-sm-7">
                <!--                <img src="--><?//= \yii\helpers\Url::to("@web".$info['image'])?><!--" alt="加载失败" height="150" width="150">-->
                <?php
                $images = $info['images'];
                $imagesArray = explode(",",$images);
                foreach ($imagesArray as $key => $value){
                    echo '<img src="' . \yii\helpers\Url::to("@web".$value) . '"onclick="show_img(this)"  alt="加载失败" height="150" width="150">';
                }
                ?>
            </div>
        </div>
        <div class="layui-form">
            <div class="layui-form-item" id="area-picker">
                <div class="col-sm-3 control-label">
                    <label >选择地区:</label>
                </div>
                <div class="layui-input-inline col-sm-7">
                    <select name="province" id="province" class="province-selector" data-value="<?= $region['areaParentParentName']?>" lay-filter="province-1">
                        <option value="">请选择省</option>
                    </select>
                </div>
                <?php
                //var_dump($region);
                //var_dump($region['areaParentName']);
                ?>
                <div class="layui-input-inline">
                    <select name="city" id="city" class="city-selector" data-value="<?= $region['areaParentName']?>" lay-filter="city-1">
                        <option value="">请选择市</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="region" id="region" class="county-selector" data-value="<?= $region['areaName']?>" lay-filter="county-1">
                        <option value="">请选择县/区</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">收货地址：</label>
            <div class="input-group col-sm-7">
                <input id="addr" type="text" class="form-control" name="addr"  value="<?= $info['addr']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">订单备注：</label>
            <div class="input-group col-sm-7">
                <input id="memo" type="text" class="form-control" name="memo" required=""  aria-required="true" value="<?= $info['memo'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">状态：</label>
            <div class="input-group col-sm-7">
                <input id="status" type="text" class="form-control" required="" readonly="readonly" aria-required="true" value="<?= $info['status'] ?>">
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