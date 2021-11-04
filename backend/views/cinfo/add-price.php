<?php

use yii\helpers\Html;
use backend\assets\AppAsset;
use common\widgets\Alert;

/* @var $this yii\web\View */
/* @var $cinfo app\backend\models\Info */
/* @var $dicts 状态列表 */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
    <link href="/static/css/upload.css" rel="stylesheet">
    <?php $this->head() ?>
    <style>
        .img-container{
            height: 200px;
            overflow-y: scroll;
        }
        .img-container img{
            max-height: 100%;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<br>
<br>
<div class="wrap">
    <div class="container" style="float: left; margin-left: 1%">
        <?= Alert::widget() ?>
        <div class="customer-form">
            <div class="row">
                <label class="col-sm-4 control-label">有奖图片（再次添加时即更新原先的图片）：</label>
                <div class="col-sm-12">
                    <input type="text" class="layui-btn goods_cov" readonly="readonly" value="选择封面" lay-data="{url: '/cinfo/prc'}" >
                    <button type="button" class="layui-btn" id="uploadImg"><i class="layui-icon"></i>上传文件</button>
                    <blockquote class="layui-elem-quote layui-quote-nm"  style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="info_cov"></div>
                    </blockquote>
                    </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<script src="/static/js/layui/layui.js"></script>
<script src="/static/js/upload.js"></script>

<script>


    $(function(){
        $(document).on('beforeSubmit', 'form#infoForm', function () {
            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                success: function (res){
                    if(res.code){
                        layer.msg(res.msg);
                        window.parent.initTable();
                        setTimeout(function(){
                            var p = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(p);
                        }, 1000);
                    }
                },
                error  : function (){
                    layer.msg(res.msg, {anim: 6});
                    return false;
                }
            });
            return false;
        });
    });

    layui.use(['upload'], function() {
        var upload = layui.upload;

        //执行上传封面实例
        upload.render({
            elem: '.goods_cov' //绑定元素
            , method: 'post'
            , multiple: false
            , auto: false
            , bindAction: '#uploadImg'
            , choose: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#previewImg').remove();
                    $('#info_cov').append('<img src="'+ result +'" alt="'+ file.name +'" id="previewImg" style="margin-right: 3px" class="layui-upload-img">')
                });
            }
            , done: function (res) {
                // var jsonData = JSON.stringify(res);// 转成JSON格式
                // var result = $.parseJSON(jsonData);// 转成JSON对象
                // setTimeout(layer.alert(result.msg), 5500);
                // if (res.code == 1) {
                //
                //     $(".infoImg").attr("value", res.path);
                // }
                layer.alert('封面上传成功');

                // console.log(result);
            }
            , error: function () {
                layer.alert('上传失败');
            }
        });

    })

</script>
