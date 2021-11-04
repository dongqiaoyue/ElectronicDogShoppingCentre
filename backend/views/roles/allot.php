<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>分配权限</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/css/plugins/iCheck/custom.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="ibox-content">
    <form class="form-horizontal m-t" method="post" action="/roles/allot" id="commentForm">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <input name="role_id" type="hidden"  value="<?= $role_id; ?>">
        <?php foreach($nodes as $key => $vo): ?>
        <div class="p-node">
            <div class="form-group parent" data-id="<?= $vo['ID'] ?>">
                <label class="col-sm-2 control-label"></label>
                <div class="input-group col-sm-9">
                    <label class="checkbox-inline i-checks">
                        <input type="checkbox" value="<?= $vo['ID'] ?>" name="rules[]" <?php if(in_array($vo['ID'], $info)): ?>checked<?php endif; ?>>
                        <?= $vo['Name']; ?>
                    </label>
                </div>
            </div>
            <?php if(!empty($vo['children'])): ?>
                <?php foreach($vo['children'] as $k => $v): ?>
                    <div class="c-node" data-id="<?= $v['ID'] ?>">
                        <div class="form-group children">
                            <label class="col-sm-2 control-label"></label>
                            <div class="input-group col-sm-9">
                                <div style="margin-left: 30px"> |--
                                    <label class="checkbox-inline i-checks">
                                        <input type="checkbox" value="<?= $v['ID'] ?>" name="rules[]" <?php if(in_array($v['ID'], $info)): ?>checked<?php endif; ?>>
                                        <?= $v['Name'] ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="s-node">
                            <?php if(!empty($v['children'])): ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"></label>
                                    <div class="input-group col-sm-9">
                                        <div style="margin-left: 60px"> |--
                                            <?php foreach($v['children'] as $m => $l): ?>
                                                <label class="checkbox-inline i-checks">
                                                    <input type="checkbox" value="<?= $l['ID'] ?>" name="rules[]" <?php if(in_array($l['ID'], $info)): ?>checked<?php endif; ?>>
                                                    <?= $l['Name'] ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <div class="form-group">
            <div class="col-sm-7 col-sm-offset-9">
                <button class="btn btn-mint btn-success" id="sub">确认分配</button>
            </div>
        </div>
    </form>
</div>
<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/js/content.min.js?v=1.0.0"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script src="/static/js/jquery.form.js"></script>
<script src="/static/js/plugins/iCheck/icheck.min.js"></script>
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

        $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green"});

        var lock = 2; // 锁定选中2级不触发一级的动作
        var lock2 = 2; // 锁定选中3级不触发二级的动作

        // 选父层，将子层全选
        $('.parent').find('input[name="rules[]"]').on('ifChecked', function(event){
            if(2 == lock){
                $(this).parents('.p-node').find('input[name="rules[]"]').iCheck('check');
            }
        });

        //不选择父层,全部取消
        $('.parent').on('ifUnchecked', function(event){
            if(2 == lock){
                $(this).parents('.p-node').find('input[name="rules[]"]').iCheck('uncheck');
            }
        });

        // 二层选择，选中其父层 和 子层
        $('.c-node .children').find('input[name="rules[]"]').on('ifChecked', function(event){
            lock = 1;

            $(this).parents('.c-node').siblings('.parent').find('input[name="rules[]"]').iCheck('check'); // 选中父层
            if(2 == lock2){
                $(this).parents('.c-node').children('.s-node').find('input[name="rules[]"]').iCheck('check'); // 选中子层
            }

            lock = 2;
        });

        // 不二层选择，不选中其父层 和 不子层
        $('.c-node .children').find('input[name="rules[]"]').on('ifUnchecked', function(event){
            lock = 1;

            // $(this).parents('.c-node').siblings('.parent').find('input[name="rules[]"]').iCheck('uncheck'); // 清除父层
            $(this).parents('.c-node').children('.s-node').find('input[name="rules[]"]').iCheck('uncheck'); // 清除子层

            lock = 2;
        });

        // 第三层选择
        $('.s-node').find('input[name="rules[]"]').on('ifChecked', function(event){
            lock = 1;
            lock2 = 1;

            $(this).parents('.c-node').children('.children').find('input[name="rules[]"]').iCheck('check');
            $(this).parents('.c-node').siblings('.parent').find('input[name="rules[]"]').iCheck('check');

            lock = 2;
            lock2 = 2;
        });
    });

</script>

</body>
</html>