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
    <link href="/static/css/video-js.min.css" rel="stylesheet">
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
                <?= Html::activeHiddenInput($cinfo, "id"); ?>
                <div class="col-sm-12">
                    <?= $form->field($cinfo, 'status')->label('信息类别')->dropDownList($listData, ['prompt' => '选择类别', 'style' => 'width:120px']) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($cinfo, 'title')->label('信息标题')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($cinfo, 'sort')->label('排序')->textInput(['maxlength' => true]) ?>
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
<!--                    <input type="text" class="layui-btn goods_cov" readonly="readonly" value="选择封面" lay-data="{url: '/cinfo/cov?del=1&id=--><?//=$id?><!--'}" >-->
<!--                    <button type="button" class="layui-btn" id="uploadImg"><i class="layui-icon"></i>上传文件</button>-->
<!--                    <a href="javascript:cinfoDelCov();">-->
<!--                        <button type="button" class="layui-btn" id="deleteImg" value="--><?//=$id?><!--"><i class="layui-icon"></i>删除文件</button>-->
<!--                    </a>-->
                    <blockquote class="layui-elem-quote layui-quote-nm"  style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="info_cov">
                            <?php if($tid){foreach ($cover as $key){?>
                                <img src="<?php if(isset($key['1'])){echo $key['1'];}else{echo '';}?>" id="previewImg" style="height:20%; width: 20%; margin-right: 3px" class="layui-upload-img">
                            <?php }}else{?>
                                <img src="<?=$cinfo->cover?>" id="previewImg" style="height:20%; width: 20%; margin-right: 3px" class="layui-upload-img">
                            <?php }?>
                            <?php if($tid){foreach ($cover_copy as $key){?>
                                <img src="<?php if(isset($key['1'])){echo $key['1'];}else{echo '';}?>" id="previewImg" style="height:20%; width: 20%; margin-right: 3px" class="layui-upload-img">
                            <?php }}?>
                        </div>
                    </blockquote>
                    <?= $form->field($cinfo, 'cover')->textInput(['maxlength' => true, 'readonly' => true,'style' => 'margin-top: 8px; display: none;','class' => 'form-control infoImg'])->label(false) ?>
                    <br>
                    <br>
                    <br>
                </div>

                <label class="col-sm-12 control-label" style="margin-top: 10px">宣传视频：</label>
                <div class="layui-upload">
                    <br>
                    <button type="button" class="layui-btn layui-btn-normal " id="testList">选择多文件</button>
                    <div class="layui-upload-list">
                        <table class="layui-table">
                            <thead>
                            <tr><th>文件名</th>
                                <th>大小</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr></thead>
                            <tbody id="demoList"></tbody>
                        </table>
                    </div>
                    <button type="button" class="layui-btn testListAction">开始上传</button>
                    <br>
                    <br>
                    <br>
                </div>
                <hr>
                <select name="city" lay-verify="" style="width: 150px;" class="layui-input selAtt">
                    <option value="">请选择一个删除</option>
                    <?php foreach ($view as $key) { ?>
                        <option value="<?=$key?>"><?=$key?></option>
                    <?php }?>
                </select>
                <br>
                <a href="javascript:cinfoDelAtt();">
                    <button type="button" class="layui-btn deleteAtt"  value="<?='1;'.$id?>"><i class="layui-icon"></i>删除视频</button>
                </a>
                <?php foreach($view as $key) {?>
                <div class="col-sm-12 previewDiv">
                    <br>
                    <br>
                    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                        <legend><?php if($key){echo $key;}else{echo '无视频文件';}?></legend>
                    </fieldset>
                    <br>
                    <video id="my-video" class="video-js" controls preload="auto" width="960" height="400"
                           poster="m.jpg" data-setup="{}">
                        <source src="<?=$key?>" type="video/mp4">
                        <source src="<?=$key?>" type="video/ogg">
                        <source src="<?=$key?>" type="video/webm">
                        <p class="vjs-no-js"> 视频格式浏览器不支持 </p>
                    </video>
                    <br>
                    <br>
                    <br>
                </div>
                <?php }?>

                <label class="col-sm-12 control-label" style="margin-top: 10px">使用教程：</label>
                <div class="layui-upload">
                    <br>
                    <button type="button" class="layui-btn layui-btn-normal " id="testList2">选择多文件</button>
                    <div class="layui-upload-list">
                        <table class="layui-table">
                            <thead>
                            <tr><th>文件名</th>
                                <th>大小</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr></thead>
                            <tbody id="demoList2"></tbody>
                        </table>
                    </div>
                    <button type="button" class="layui-btn testListAction2">开始上传</button>
                    <br>
                    <br>
                    <br>
                </div>
                <hr>
                <select name="city" lay-verify="" style="width: 150px;" class="layui-input selAtt2">
                    <option value="">请选择一个删除</option>
                    <?php foreach ($teach as $key) { ?>
                        <option value="<?=$key?>"><?=$key?></option>
                    <?php }?>
                </select>
                <br>
                <a href="javascript:cinfoDelAtt2();">
                    <button type="button" class="layui-btn deleteAtt2"  value="<?='0;'.$id.';'.$key?>"><i class="layui-icon"></i>删除视频</button>
                </a>
                <?php foreach ($teach as $key) { ?>
                <div class="col-sm-12 teacherDiv">
                    <br>
                    <br>
                    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                        <legend><?php if($key){echo $key;}else{echo '无视频文件';}?></legend>
                    </fieldset>

                    <br>
                    <br>
                    <video id="my-video" class="video-js" controls preload="auto" width="960" height="400"
                           poster="m.jpg" data-setup="{}">
                        <source src="<?=$key?>" type="video/mp4">
                        <source src="<?=$key?>" type="video/ogg">
                        <source src="<?=$key?>" type="video/webm">
                        <p class="vjs-no-js"> 视频格式浏览器不支持 </p>
                    </video>
                    <br>
                    <br>
                    <br>
                </div>
                <?php }?>
            </div>
            <br>

            <div >
                <button class="btn btn-primary" type="submit">更新</button>
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
<script src="/static/js/video.min.js"></script>

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

    //设置中文
    videojs.addLanguage('zh-CN', {
        "Play": "播放",
        "Pause": "暂停",
        "Current Time": "当前时间",
        "Duration": "时长",
        "Remaining Time": "剩余时间",
        "Stream Type": "媒体流类型",
        "LIVE": "直播",
        "Loaded": "加载完毕",
        "Progress": "进度",
        "Fullscreen": "全屏",
        "Non-Fullscreen": "退出全屏",
        "Mute": "静音",
        "Unmute": "取消静音",
        "Playback Rate": "播放速度",
        "Subtitles": "字幕",
        "subtitles off": "关闭字幕",
        "Captions": "内嵌字幕",
        "captions off": "关闭内嵌字幕",
        "Chapters": "节目段落",
        "Close Modal Dialog": "关闭弹窗",
        "Descriptions": "描述",
        "descriptions off": "关闭描述",
        "Audio Track": "音轨",
        "You aborted the media playback": "视频播放被终止",
        "A network error caused the media download to fail part-way.": "网络错误导致视频下载中途失败。",
        "The media could not be loaded, either because the server or network failed or because the format is not supported.": "视频因格式不支持或者服务器或网络的问题无法加载。",
        "The media playback was aborted due to a corruption problem or because the media used features your browser did not support.": "由于视频文件损坏或是该视频使用了你的浏览器不支持的功能，播放终止。",
        "No compatible source was found for this media.": "无法找到此视频兼容的源。",
        "The media is encrypted and we do not have the keys to decrypt it.": "视频已加密，无法解密。",
        "Play Video": "播放视频",
        "Close": "关闭",
        "Modal Window": "弹窗",
        "This is a modal window": "这是一个弹窗",
        "This modal can be closed by pressing the Escape key or activating the close button.": "可以按ESC按键或启用关闭按钮来关闭此弹窗。",
        ", opens captions settings dialog": ", 开启标题设置弹窗",
        ", opens subtitles settings dialog": ", 开启字幕设置弹窗",
        ", opens descriptions settings dialog": ", 开启描述设置弹窗",
        ", selected": ", 选择",
        "captions settings": "字幕设定",
        "Audio Player": "音频播放器",
        "Video Player": "视频播放器",
        "Replay": "重播",
        "Progress Bar": "进度小节",
        "Volume Level": "音量",
        "subtitles settings": "字幕设定",
        "descriptions settings": "描述设定",
        "Text": "文字",
        "White": "白",
        "Black": "黑",
        "Red": "红",
        "Green": "绿",
        "Blue": "蓝",
        "Yellow": "黄",
        "Magenta": "紫红",
        "Cyan": "青",
        "Background": "背景",
        "Window": "视窗",
        "Transparent": "透明",
        "Semi-Transparent": "半透明",
        "Opaque": "不透明",
        "Font Size": "字体尺寸",
        "Text Edge Style": "字体边缘样式",
        "None": "无",
        "Raised": "浮雕",
        "Depressed": "压低",
        "Uniform": "均匀",
        "Dropshadow": "下阴影",
        "Font Family": "字体库",
        "Proportional Sans-Serif": "比例无细体",
        "Monospace Sans-Serif": "单间隔无细体",
        "Proportional Serif": "比例细体",
        "Monospace Serif": "单间隔细体",
        "Casual": "舒适",
        "Script": "手写体",
        "Small Caps": "小型大写字体",
        "Reset": "重启",
        "restore all settings to the default values": "恢复全部设定至预设值",
        "Done": "完成",
        "Caption Settings Dialog": "字幕设定视窗",
        "Beginning of dialog window. Escape will cancel and close the window.": "开始对话视窗。离开会取消及关闭视窗",
        "End of dialog window.": "结束对话视窗"
    });

    function cinfoDelCov(){
        var id = $("#deleteImg").attr("value");
        layer.confirm('确认删除此图片?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/cinfo/del-cov", {'id' : id}, function(res){
                if(1 == res.code){
                    layer.msg(res.msg);
                    // setTimeout(function(){
                    //     initTable();
                    // }, 1000);
                    $('#previewImg').remove();
                }else if(111 == res.code){
                    window.location.reload();
                }else{
                    layer.alert(res.msg, {title: '友情提示', icon: 2});
                }
            });

            layer.close(index);
        })

    }
    function cinfoDelAtt2(){
        var str = $(".deleteAtt2").attr("value");
        var att = $(".selAtt2 option:selected").val();
        console.log(att)
        layer.confirm('确认删除此视频?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/cinfo/del-att", {'str' : str, 'att' : att}, function(res){
                if(1 == res.code){
                    layer.msg(res.msg);
                    // setTimeout(function(){
                    //     initTable();
                    // }, 1000);
                    // $('#previewImg').remove();
                    // $('.goodsVid').val('');
                    window.location.reload();
                }else if(111 == res.code){
                    window.location.reload();
                }else{
                    layer.alert(res.msg, {title: '友情提示', icon: 2});
                }
            });

            layer.close(index);
        })

    }

    function cinfoDelAtt(){
        var str = $(".deleteAtt").attr("value");
        var att = $(".selAtt option:selected").val();
        // console.log(att)
        layer.confirm('确认删除此视频?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/cinfo/del-att", {'str' : str, 'att' : att}, function(res){
                if(1 == res.code){
                    layer.msg(res.msg);
                    // setTimeout(function(){
                    //     initTable();
                    // }, 1000);
                    // $('#previewImg').remove();
                    // $('.goodsVid').val('');
                    window.location.reload();
                }else if(111 == res.code){
                    window.location.reload();
                }else{
                    layer.alert(res.msg, {title: '友情提示', icon: 2});
                }
            });

            layer.close(index);
        })

    }

    var myPlayer = videojs('my-video');
    videojs("my-video").ready(function(){
        var myPlayer = this;
        myPlayer.play();
    });


    layui.use(['upload'], function() {
        var upload = layui.upload;
        var id = $("#deleteImg").attr("value");
        // var type = 's';
        var demoListView = $('#demoList')
            ,uploadListIns = upload.render({
            elem: '#testList'
            ,url: "/cinfo/mul-vid?type=1&id="+id
            ,accept: 'file'
            ,multiple: true
            ,auto: false
            ,bindAction: '.testListAction'
            ,choose: function(obj){
                // console.log('ssss')
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                //读取本地文件
                obj.preview(function(index, file, result){
                    console.log(index)
                    var tr = $(['<tr id="upload-'+ index +'">'
                        ,'<td>'+ file.name +'</td>'
                        ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                        ,'<td>等待上传</td>'
                        ,'<td>'
                        ,'<button type="button" class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                        ,'<button type="button" class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                        ,'</td>'
                        ,'</tr>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function(){
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function(){
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    demoListView.append(tr);
                });
            }
            // ,before: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
            //     layer.load(); //上传loading
            // }
            ,progress: function(n){
                var percent = n + '%' //获取进度百分比
                element.progress('demo', percent); //可配合 layui 进度条元素使用
            }
            ,done: function(res, index, upload){
                console.log(res)
                if(res.code == 1){ //上传成功
                    var tr = demoListView.find('tr#upload-'+ index)
                        ,tds = tr.children();
                    tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                    tds.eq(3).html(''); //清空操作
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                }
                this.error(index, upload);
            }
            ,error: function(index, upload){
                var tr = demoListView.find('tr#upload-'+ index)
                    ,tds = tr.children();
                tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
                tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
        });

        var ListView = $('#demoList2')
            ,uploadListViewIns = upload.render({
            elem: '#testList2'
            ,url: "/cinfo/mul-vid?type=2&id="+id
            ,accept: 'file'
            ,multiple: true
            ,auto: false
            ,bindAction: '.testListAction2'
            ,choose: function(obj){
                // console.log('ssss')
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                //读取本地文件
                obj.preview(function(index, file, result){
                    console.log(index)
                    var tr = $(['<tr id="upload-'+ index +'">'
                        ,'<td>'+ file.name +'</td>'
                        ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                        ,'<td>等待上传</td>'
                        ,'<td>'
                        ,'<button type="button" class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                        ,'<button type="button" class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                        ,'</td>'
                        ,'</tr>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function(){
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function(){
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListViewIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    ListView.append(tr);
                });
            }
            ,allDone: function(obj){ //当文件全部被提交后，才触发
                console.log('22');
                layer.closeAll('loading'); //关闭loading
            }
            ,done: function(res, index, upload){
                console.log(res)
                if(res.code == 1){ //上传成功
                    var tr = ListView.find('tr#upload-'+ index)
                        ,tds = tr.children();
                    tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                    tds.eq(3).html(''); //清空操作
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                    // window.location.reload();
                }
                this.error(index, upload);
            }
            ,error: function(index, upload){
                var tr = ListView.find('tr#upload-'+ index)
                    ,tds = tr.children();
                tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
                tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
        });

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
                    $(".infoImg").removeAttr("value");

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
