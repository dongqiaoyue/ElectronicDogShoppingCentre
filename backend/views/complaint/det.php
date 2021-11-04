<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投诉详情</title>
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
            <label class="col-sm-3 control-label">投诉ID：</label>
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
            <label class="col-sm-3 control-label">投诉原因：</label>
            <div class="input-group col-sm-7">
                <input id="title" type="text" class="form-control" name="title" required="" readonly="readonly" aria-required="true" value="<?= $info['title'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">处理期望：</label>
            <div class="input-group col-sm-7">
                <input id="expectation" type="text" class="form-control" name="title" required="" readonly="readonly" aria-required="true" value="<?= $info['expectation'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">投诉内容：</label>
            <div class="input-group col-sm-7">
                <input id="content" type="text" class="form-control" name="content" required="" readonly="readonly" aria-required="true" value="<?= $info['content'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">图片：</label>
            <div class="input-group col-sm-7">
                <div  class="layer-photos-demo">
<!--                <img src="--><?//= \yii\helpers\Url::to("@web".$info['image'])?><!--" alt="加载失败" height="150" width="150">-->
                <?php
                $images = $info['image'];
                $imagesArray = explode(",",$images);
                foreach ($imagesArray as $key => $value){
                    //echo '<img layer-src="' . \yii\helpers\Url::to("@web".$value) . '" src="' . \yii\helpers\Url::to("@web".$value) . '"  alt="加载失败" height="150" width="150">';
                    echo '<img src="' . \yii\helpers\Url::to("@web".$value) . '"  alt="加载失败" height="150" width="150">';
                }
                ?>
                </div>
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
            area: ['100%', '100%'], //宽高
            shadeClose: true, //开启遮罩关闭
            end: function (index, layero) {
                return false;
            },
            content: '<div style="text-align:center"><img src="' + $(t).attr('src') + '" height="100%" width="100%"/></div>'
        });


        // var img = new Image();
        // img.src = t.src;
        // var height = img.height +50; //获取图片高度
        // var width = img.width; //获取图片宽度
        // var imgHtml = "<img src='" + t.src + "' />";
        // //弹出层
        // layer.open({
        //     type: 1,
        //     shade: 0.8,
        //     offset: 'auto',
        //     area: [width + 'px',height+'px'],
        //     shadeClose:true,//点击外围关闭弹窗
        //     scrollbar: false,//不现实滚动条
        //     title: "图片预览", //不显示标题
        //     content: imgHtml, //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
        //     cancel: function () {
        //         //layer.msg('捕获就是从页面已经存在的元素上，包裹layer的结构', { time: 5000, icon: 6 });
        //     }
        // });
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