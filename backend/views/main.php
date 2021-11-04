<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <title>华复实业销售平台</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html"/>
    <![endif]-->
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
</head>

<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
<div id="wrapper">
    <!--左侧导航开始-->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="nav-close"><i class="fa fa-times-circle"></i>
        </div>
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><img alt="image" class="img-circle" src="/static/images/profile_small.jpg"/></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <br>
                                <span class="block m-t-xs">欢迎您 -- <strong class="font-bold"><?php if(\common\helpers\Tools::authCheck('admins/add')) echo '超级管理员'; else echo '客服';?></strong></span>
                            </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li>
                                <a href="javascript:change()">修改密码</a>
                            </li>
                            <li>
                                <a href="/login/login-out">安全退出</a>
                            </li>
                        </ul>
                    </div>
                    <div class="logo-element">AD
                    </div>
                </li>
                <!-- 菜单开始 -->
                <?php if(!empty($menu)): ?>
                    <?php foreach($menu as $key => $vo): ?>
                        <li class="menu">
                            <?php if(!empty($vo['children'])){ //若存在子级?>
                                <a href="<?= $vo['auth_rule'] ?>">
                                    <i class="<?= $vo['style'] ?>"></i>
                                    <span class="nav-label"><?= $vo['Name'] ?></span>
                                    <span class="fa arrow"></span>
                                </a>
                                <ul class="nav nav-second-level">
                                    <?php foreach($vo['children'] as $k => $v): ?>
                                        <li>
                                            <a class="J_menuItem" href="/<?= $v['auth_rule'] ?>"><?= $v['Name'] ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php }else{ //若不存在子级?>
                                <a class="J_menuItem" href="<?= $vo['auth_rule'] ?>">
                                    <i class="<?= $vo['style'] ?>"></i>
                                    <span class="nav-label"><?= $vo['Name'] ?></span>
                                </a>
                            <?php }?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <!-- 菜单结束 -->
            </ul>
        </div>
    </nav>
    <!--左侧导航结束-->
    <!--右侧部分开始-->
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                class="fa fa-bars"></i> </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li class="hidden-xs">
                        <a class="J_menuItem" id="clear"><i class="fa fa-trash"></i>
                            清理缓存
                        </a>
                    </li>
                    <li class="dropdown hidden-xs">
                        <a class="right-sidebar-toggle" aria-expanded="false">
                            <i class="fa fa-tasks"></i> 主题
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row content-tabs">
            <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
            </button>
            <nav class="page-tabs J_menuTabs">
                <div class="page-tabs-content">
                    <a href="javascript:;" class="active J_menuTab" data-id="index_v1.html">首页</a>
                </div>
            </nav>
            <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
            </button>
            <div class="btn-group roll-nav roll-right">
                <button class="dropdown J_tabClose" data-toggle="dropdown">常用操作<span class="caret"></span>

                </button>
                <ul role="menu" class="dropdown-menu dropdown-menu-right">
                    <li class="J_tabGo"><a>前进</a></li>
                    <li class="J_tabBack"><a>后退</a></li>
                    <li class="J_tabFresh"><a>刷新</a></li>
                    <li class="divider"></li>
                    <li class="J_tabShowActive"><a>定位当前选项卡</a></li>
                    <li class="divider"></li>
                    <li class="J_tabCloseAll"><a>关闭全部选项卡</a></li>
                    <li class="J_tabCloseOther"><a>关闭其他选项卡</a></li>
                </ul>
            </div>
            <a href="/login/login-out" class="roll-nav roll-right J_tabExit">
                <i class="fa fa fa-sign-out"></i>
                退出
            </a>
        </div>
        <div class="row J_mainContent" id="content-main">
            <iframe class="J_iframe" name="iframe0" width="100%" height="100%"
                    src="/site/first" frameborder="0"
                    data-id="index_v1.html" seamless></iframe>
        </div>
        <div class="footer">
            <div class="pull-right">&copy; 2018-2020
            </div>
        </div>
    </div>
    <!--右侧部分结束-->
    <!--右侧边栏开始-->
    <div id="right-sidebar">
        <div class="sidebar-container">
            <ul class="nav nav-tabs navs-3">
                <li class="active">
                    <a data-toggle="tab" href="#tab-1">
                        <i class="fa fa-gear"></i> 主题
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="sidebar-title">
                        <h3> <i class="fa fa-comments-o"></i> 主题设置</h3>
                        <small><i class="fa fa-tim"></i> 你可以从这里选择和预览主题的布局和样式，这些设置会被保存在本地，下次打开的时候会直接应用这些设置。</small>
                    </div>
                    <div class="skin-setttings">
                        <div class="title">主题设置</div>
                        <div class="setings-item">
                            <span>收起左侧菜单</span>
                            <div class="switch">
                                <div class="onoffswitch">
                                    <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="collapsemenu">
                                    <label class="onoffswitch-label" for="collapsemenu">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="setings-item">
                            <span>固定顶部</span>

                            <div class="switch">
                                <div class="onoffswitch">
                                    <input type="checkbox" name="fixednavbar" class="onoffswitch-checkbox" id="fixednavbar">
                                    <label class="onoffswitch-label" for="fixednavbar">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="setings-item">
                                <span>
                        固定宽度
                    </span>

                            <div class="switch">
                                <div class="onoffswitch">
                                    <input type="checkbox" name="boxedlayout" class="onoffswitch-checkbox" id="boxedlayout">
                                    <label class="onoffswitch-label" for="boxedlayout">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="title">皮肤选择</div>
                        <div class="setings-item default-skin nb">
                                <span class="skin-name ">
                         <a href="#" class="s-skin-0">
                             默认皮肤
                         </a>
                    </span>
                        </div>
                        <div class="setings-item blue-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-1">
                            蓝色主题
                        </a>
                    </span>
                        </div>
                        <div class="setings-item yellow-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-3">
                            黄色/紫色主题
                        </a>
                    </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--右侧边栏结束-->
    <!--mini聊天窗口开始-->
</div>

<!-- 修改个人信息 -->
<div class="ibox-content" id="box" style="display: none">
    <form class="form-horizontal m-t" method="post" action="{:url('index/changePassword')}" id="editForm">
        <div class="form-group">
            <label class="col-sm-3 control-label">旧密码：</label>
            <div class="input-group col-sm-7">
                <input id="old_pwd" type="password" class="form-control" name="old_pwd" placeholder="请输入旧密码">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">新密码：</label>
            <div class="input-group col-sm-7">
                <input id="new_pwc" type="password" class="form-control" name="password" placeholder="请输入新密码">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-8">
                <button class="btn btn-primary" type="submit">确认修改</button>
            </div>
        </div>
    </form>
</div>
<!-- 修改个人信息 -->

<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/static/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/hplus.min.js?v=4.1.0"></script>
<script src="/static/js/contabs.js"></script>
<script src="/static/js/plugins/pace/pace.min.js"></script>
<script src="/static/js/layui/layui.js"></script>
<script src="/static/js/jquery.form.js"></script>
<script>
    var box;
    function change(){


        layui.use(['layer'], function(){
            box = layer.ready(function(){

                box = layer.open({
                    type: 1,
                    title: '修改个人信息',
                    anim: 2,
                    skin: 'layui-layer-molv', //加上边框
                    area: ['620px', '250px'], //宽高
                    content: $('#box')
                });
            });

        });
    }

    function showStart(){
        return true;
    }

    function showSuccess(res){
        layui.use(['layer'], function(){
            layer.ready(function(){
                if(1 == res.code){
                    layer.alert(res.msg, {title: '友情提示', icon: 1, closeBtn: 0}, function(){
                        window.location.href = "{:url('login/loginOut')}";
                    });
                }else if(111 == res.code){
                    window.location.reload();
                }else{
                    layer.msg(res.msg, {anim: 6});
                }
            });
        });
    }

    $(function(){

        var options = {
            beforeSubmit:showStart,
            success:showSuccess
        };

        $('#editForm').submit(function(){
            $(this).ajaxSubmit(options);
            return false;
        });

        $("#clear").click(function(){
            $.getJSON("{:url('index/clear')}", function(res){
                if(1 == res.code){
                    layer.tips(res.msg, "#clear", {time: 1500});
                }else{
                    layer.tips(res.msg, "#clear", {time: 1500});
                }
            });
        });

    });


</script>
</body>
</html>