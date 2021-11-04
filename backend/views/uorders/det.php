<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户订单详情</title>
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
        <button type="button" id="track" class="btn btn-primary">订单跟踪</button>
        <div class="form-group">
            <label class="col-sm-3 control-label">状态：</label>
            <div class="input-group col-sm-7">
                <input id="status" type="text" class="form-control" name="status" required="" readonly="readonly" aria-required="true" value="<?= $info['status'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">订单号：</label>
            <div class="input-group col-sm-7">
                <input id="ID" type="text" class="form-control" name="ID" required="" aria-required="true" readonly="readonly" value="<?= $info['ID']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">用户电话号码：</label>
            <div class="input-group col-sm-7">
                <input id="phone" type="text" class="form-control" name="phone" readonly="readonly" value="<?= $info['phone']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">收货人姓名：</label>
            <div class="input-group col-sm-7">
                <input id="agentName" type="text" class="form-control" name="agentName" required="" aria-required="true" readonly="readonly" value="<?= $info['receiver'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">收货人联系电话：</label>
            <div class="input-group col-sm-7">
                <input id="phone" type="text" class="form-control" name="phone" required="" aria-required="true" readonly="readonly" value="<?= $info['receiverPhone'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">购买详情：</label>
        </div>
        <div class="layui-form col-sm-offset-3" style="width: 60%">
            <table class="layui-table ">
                <colgroup>
                    <col width="150">
                    <col width="150">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>商品名称</th>
                    <th>图片</th>
                    <th>sku分类</th>
                    <th>价格</th>
                    <th>数量</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($details as $key => $value){
                    echo '<tr>';
                    echo '<th>' . $details[$key]['title'] . '</th>';
                    //echo '<th><img src="' . \yii\helpers\Url::to("@web".$details[$key]['image']) . '" width="50%" height="5%" " onclick="show_img(this)"> </th>';
                    //echo '<th><div class="layer-photos-demo"><img layer-src=" ' . \yii\helpers\Url::to("@web".$value) . '" src="' . \yii\helpers\Url::to("@web".$value) . '" width="50%" height="5%"  ></div></th>';
                    echo '<th><div class="layer-photos-demo">';
                    echo '<img src="' . \yii\helpers\Url::to("@web".$details[$key]['image']) . '" width="50%" height="5%" >';
                    echo '</div></th>';
                    echo '<th>' . $details[$key]['name'] . '</th>';
                    echo '<th>' . $details[$key]['price'] . '</th>';
                    echo '<th>' . $details[$key]['numbers'] . '</th>';
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">收货地址：</label>
            <div class="input-group col-sm-7">
                <input id="userAddr" type="text" class="form-control" name="userAddr" required="" readonly="readonly" aria-required="true" value="<?= $info['userAddr'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">运单号：</label>
            <div class="input-group col-sm-7">
                <input id="trackID" type="text" class="form-control" name="trackID" readonly="readonly" value="<?= $info['trackID']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">总金额：</label>
            <div class="input-group col-sm-7">
                <input id="totalMoney" type="text" class="form-control" name="totalMoney" required="" readonly="readonly" aria-required="true" value="<?= $info['totalMoney'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">快递公司：</label>
            <div class="input-group col-sm-7">
                <input id="postName" type="text" class="form-control" name="postName" readonly="readonly" value="<?= $info['postName']?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">备注：</label>
            <div class="input-group col-sm-7">
                <input id="memo" type="text" class="form-control" name="memo" readonly="readonly" value="<?= $info['memo']?>">
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

    $(document).ready(function () {
        //物流跟踪
        $("#track").bind("click",track);
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
            content: '<div style="text-align:center"><img src="' + $(t).attr('src') + '" height="100%" width="100%" /></div>'
        });
    }
    //物流跟踪q
    function track() {
        //console.log("321");
        layer.open({
            type: 2,
            title: '订单跟踪',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/uorders/track?id='+ "<?=$info['ID']?>"+"&trackID="+"<?=$info['trackID']?>"
        });
        //console.log("123");
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