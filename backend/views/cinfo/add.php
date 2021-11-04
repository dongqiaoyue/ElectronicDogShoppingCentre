<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $cinfo app\backend\models\Info */
/* @var $dicts 状态列表 */

//形成一个数组
$listData = ArrayHelper::map($dicts, '1', '0'); // 出来的就是这个样子的['1' => '大学', '2' => '高中', '3' => '初中']


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

            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data', 'id' => 'infoForm'],
//                'enableAjaxValidation'=> true,
//                'validationUrl' => ''
            ]); ?>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($cinfo, 'status')->label('信息类别')->dropDownList($listData, ['prompt' => '选择类别', 'style' => 'width:120px']) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($cinfo, 'title')->label('信息标题')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($cinfo, 'description')->label('信息描述')->widget('common\widgets\ueditor\Ueditor',[
                        'options'=>[
                            'initialFrameWidth' => 1140,
                            'initialFrameHeight' => 400,
                            'lang' => 'zh-cn',
                            'toolbars' => [
                                [
                                    'fullscreen', 'source', 'undo', 'redo', '|',
                                    'fontsize',
                                    'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                                    'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                                    'forecolor', 'backcolor', '|',
                                    'lineheight', '|',
                                    'indent', '|',
                                    'simpleupload', //单图上传
                                    'insertimage', //多图上传
                                ],
                            ]
                        ]
                    ]) ?>
                </div>
                <label class="col-sm-3 control-label">基础信息封面：</label>
                <div class="col-sm-12">
                <input type="text" class="layui-btn goods_cov" readonly="readonly" value="选择封面" lay-data="{url: '/cinfo/cov'}" >
                <button type="button" class="layui-btn" id="uploadImg"><i class="layui-icon"></i>上传文件</button>
                <blockquote class="layui-elem-quote layui-quote-nm"  style="margin-top: 10px;">
                    预览图：
                    <div class="layui-upload-list" id="info_cov"></div>
                </blockquote>
                    <?= $form->field($cinfo, 'cover')->textInput(['maxlength' => true, 'readonly' => true,'style' => 'margin-top: 8px; display: none;','class' => 'form-control infoImg'])->label(false) ?>
                </div>
                <label class="col-sm-3 control-label" style="margin-top: 10px">宣传视频：</label>
                <div class="col-sm-12">
                    <input type="text" readonly="readonly" value="选择视频" class="layui-btn goods_vid1" lay-data="{url: '/cinfo/vid'}">

                    <?= $form->field($cinfo, 'attach')->textInput(['maxlength' => true, 'readonly' => false,'style' => 'margin-top: 8px;','class' => 'form-control goodsVid1', 'placeholder' => '/upload/video/...'])->label(false) ?>
                </div>
                <label class="col-sm-3 control-label" style="margin-top: 10px">使用教程：</label>
                <div class="col-sm-12">
                    <input type="text" readonly="readonly" value="选择视频" class="layui-btn goods_vid2" lay-data="{url: '/cinfo/vid'}">

                    <?= $form->field($cinfo, 'attach_copy')->textInput(['maxlength' => true, 'readonly' => false,'style' => 'margin-top: 8px;','class' => 'form-control goodsVid2', 'placeholder' => '/upload/video/...'])->label(false) ?>
                </div>
            </div>
            <br>

            <div >
                <button class="btn btn-primary" type="submit">创建</button>
            </div>

            <?php ActiveForm::end(); ?>

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

    // $('#infoCreate').on('click', function() {
    //
    //     $('infoForm').on('submit', function() {
    //
    //         $(this).ajaxSubmit({
    //             type: 'post', // 提交方式 get/post
    //             url: '/cinfo/cov', // 需要提交的 url
    //             // data: {
    //             //     'title': title,
    //             //     'content': content
    //             // },
    //             success: function(data) { // data 保存提交后返回的数据，一般为 json 数据
    //                 // 此处可对 data 作相关处理
    //                 alert(data.msg);
    //             }
    //             $(this).resetForm(); // 提交后重置表单
    //         });
    //         return false; // 阻止表单自动提交事件
    //     });
    // });


    layui.use(['upload'], function() {
        var upload = layui.upload;

        //执行上传封面实例
        var uploadInst = upload.render({
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
                if (res.code == 1) {

                    $(".infoImg").attr("value", res.path);
                }
                layer.alert('封面上传成功');

                // console.log(result);
            }
            , error: function () {
                layer.alert('上传失败');
            }
        });

        upload.render({
            elem: '.goods_vid1'
            ,accept: 'video' //视频
            , auto: true
            // , bindAction: '#uploadVid'
            , choose: function(obj){
                //预读本地文件示例，不支持ie8
                // obj.preview(function(index, file, result){
                //     $('#good_cov').append('<img src="'+ result +'" alt="'+ file.name +'" style="margin-right: 3px" class="layui-upload-img">')
                // });
            }
            ,done: function(res){
                var jsonData = JSON.stringify(res);// 转成JSON格式
                var result = $.parseJSON(jsonData);// 转成JSON对象
                layer.alert('视频上传成功');

                if (res.code == 1) {
                    $(".goodsVid1").attr("value", res.path);
                }
                // console.log(result);
            }
        });

        upload.render({
            elem: '.goods_vid2'
            ,accept: 'video' //视频
            , auto: true
            // , bindAction: '#uploadVid'
            , choose: function(obj){
                //预读本地文件示例，不支持ie8
                // obj.preview(function(index, file, result){
                //     $('#good_cov').append('<img src="'+ result +'" alt="'+ file.name +'" style="margin-right: 3px" class="layui-upload-img">')
                // });
            }
            ,done: function(res){
                var jsonData = JSON.stringify(res);// 转成JSON格式
                var result = $.parseJSON(jsonData);// 转成JSON对象
                layer.alert('视频上传成功');

                if (res.code == 1) {
                    $(".goodsVid2").attr("value", res.path);
                }
                // console.log(result);
            }
        });
    })

</script>
