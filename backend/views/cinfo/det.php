<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>基础信息详情</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <style>
        .ql-editor img {
            max-width: 100%;
        }
    </style>
</head>
<body class="gray-bg">
<div class="ibox-content" style="height: 1%">
    <h1><?= $info['title']?></h1>
    <hr class="layui-bg-cyan">
    <?php if($show == '1'){?>
    <label class="col-sm-12 control-label" style="margin-top: 10px">信息详情：</label>
    <div class="ql-container ql-snow" style="padding: 40px">
        <div class="ql-editor" >
            <?= $info['description']?>
        </div>
    </div>
    <div class="ql-container ql-snow">
        <?php if($id){foreach($cover as $key) {?>
            <label class="col-sm-12 control-label" style="margin-top: 10px">宣传视频：</label>
            <div class="col-sm-12 previewDiv">
                <br>
                <br>
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                    <legend><?php if($key['0']){echo $key['0'];}else{echo '无视频文件';}?></legend>
                </fieldset>
                <br>
                <video id="my-video" class="video-js" controls preload="auto" width="960" height="400"
                       poster="<?php if(isset($key['1'])){echo $key['1'];}else{echo 'm.jpg';}?>" data-setup="{}">
                    <source src="<?=$key['0']?>" type="video/mp4">
                    <source src="<?=$key['0']?>" type="video/ogg">
                    <source src="<?=$key['0']?>" type="video/webm">
                    <p class="vjs-no-js"> 视频格式浏览器不支持 </p>
                </video>
                <br>
                <br>
                <br>
            </div>
        <?php }}?>
        <?php if($id){foreach($cover_copy as $key) {?>
            <label class="col-sm-12 control-label" style="margin-top: 10px">使用教程：</label>
            <div class="col-sm-12 previewDiv">
                <br>
                <br>
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                    <legend><?php if($key['0']){echo $key['0'];}else{echo '无视频文件';}?></legend>
                </fieldset>
                <br>
                <video id="my-video" class="video-js" controls preload="auto" width="960" height="400"
                       poster="<?php if(isset($key['1'])){echo $key['1'];}else{echo 'm.jpg';}?>" data-setup="{}">
                    <source src="<?=$key['0']?>" type="video/mp4">
                    <source src="<?=$key['0']?>" type="video/ogg">
                    <source src="<?=$key['0']?>" type="video/webm">
                    <p class="vjs-no-js"> 视频格式浏览器不支持 </p>
                </video>
                <br>
                <br>
                <br>
            </div>
        <?php }}?>
        <?php if(!$id){?>
        <label class="col-sm-12 control-label" style="margin-top: 10px">信息封面：</label>
        <div class="ql-container ql-snow">
            <div class="ql-editor" style="padding: 20px">
                <img src="<?=$info['cover']?>" alt="">
            </div>
        </div>
        <?php }?>
    </div>
    <?php } else {?>
    <div class="ql-container ql-snow">
        <div class="ql-editor" style="padding: 20px">
            <img src="<?=$info['cover']?>" alt="">
        </div>
    </div>
    <?php }?>
</div>
<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/js/content.min.js?v=1.0.0"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/jquery.form.js"></script>
<script>
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
</script>
</body>
</html>