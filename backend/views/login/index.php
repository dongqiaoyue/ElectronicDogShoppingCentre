<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>华复实业销售平台</title>
    <script src="/static/js/jquery.min.js" type="text/javascript"></script>
    <script src="/static/js/layui/layui.js" type="text/javascript"></script>
    <link href="/static/css/login.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="login_box">
    <div class="login_l_img"><img src="/static/images/login-img.png" /></div>
    <div class="login">
        <div class="login_logo"><a href="javascript:;"><img src="/static/images/login_logo.png" /></a></div>
        <div class="login_name">
            <p>华复实业销售平台</p>
        </div>
        <div class="login-form">
            <input name="username" type="text" placeholder="用户名" id="u" >
            <input name="password" type="password" id="p" placeholder="密码"/>
            <div>
                <input name="img_captcha" type="text" placeholder="请输入图形验证码" id="s" class="form_input" style="width: 60%;">
                <img id="captchaImg" src="<?=\common\helpers\Tools::buildWwwUrl("/login/img_captcha");?>" onclick="this.src='<?=\common\helpers\Tools::buildWwwUrl("/login/img_captcha");?>?'+Math.random();"/>
            </div>
            <input value="登录" style="width:100%;" type="button" id="btn">
        </div>
    </div>
    <div class="copyright"><a href="javascript:; target="_blank" style="color:white;">CuitLOOP</a> 版权所有 ©2018-2019 </div>
</div>
<script>

    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==13){ // enter 键
            doLogin();
        }
    };

    $(function(){
        $("#btn").click(function(){
            doLogin();
        });
    });

    function doLogin(){
        layui.use(['layer'], function(){
            var layer = layui.layer;
            layer.ready(function(){
                var user_name = $("#u").val();
                var password = $("#p").val();
                var verifyCode = $("#s").val();

                if('' == user_name){
                    layer.tips('请输入用户名', '#u');
                    return false;
                }

                if('' == password){
                    layer.tips('请输入密码', '#p');
                    return false;
                }

                if('' == verifyCode){
                    layer.tips('请输入验证码', '#s');
                    return false;
                }

                var index = layer.load(0, {shade: false});
                var token = '<?= \Yii::$app->request->csrfToken ?>';

                function g(){
                    window.location.reload();
                }

                $.post('/login/do-login', {verifyCode: verifyCode, username: user_name, password: password, '_csrf-backend': token}, function(res){
                    layer.close(index);
                    if(1 == res.code){
                        window.location.href = res.data;
                    }else if(res.code == -6){
                        layer.msg(res.msg, {icon: 2, anim: 6});
                        setTimeout(g, 1500);
                    }else{
                        return layer.msg(res.msg, {icon: 2, anim: 6});
                    }
                }, 'json');
            });
        });
    }
</script>
</body>
</html>
