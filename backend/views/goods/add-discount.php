<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>设置优惠</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="ibox-content">
    <fieldset class="layui-elem-field layui-field-title">
        <legend><?= $title?></legend>
    </fieldset>
    <div class="form-group">
        <div class="col-sm-4 ">
            <button id="addLevel" class="btn btn-primary" type="button">新增一级优惠</button>
        </div>
    </div>
    <form class="form-horizontal m-t" method="post" action="/goods/add-discount" id="commentForm">
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= \Yii::$app->request->csrfToken ?>">
        <input name="id" type="hidden" id="_csrf" value="<?=$id ?>">
            <div class="layui-form col-sm-offset-2" style="width: 60%">
                <table class="layui-table " lay-even="" lay-skin="nob">
                    <colgroup>
                        <col width="100">
                        <col width="150">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th><strong>层级</strong></th>
                        <th><strong>优惠门槛(个)</strong></th>
                        <th><strong>单价(元)</strong></th>
                    </tr>
                    </thead>
                    <tbody id="discount">
                    <?php
                    //var_dump($discount);//string
                    //$list = implode(";",$discount);
                    //var_dump($number);
                    if(!empty($discount)){
                        //$patterns = "/\d+/";//结合正则获取字符串中数字
                        $patterns = "/(([0]\.\d{0,2}|[1-9][0-9]*\.\d{0,2}))|([1-9][0-9]*)/";//结合正则获取字符串中数字
                        preg_match_all($patterns,$discount,$arr);
                        //var_dump($arr[0]);
                        $j = 1;
                        for ($i =0 ;$i < count($arr[0]) ; $i++){
                            //var_dump((float)$arr[0][$i]);
                            echo '<tr>';
                            echo '<td>'. $j++ .'</td>';
                            echo '<td>
                                <div class="layui-input-item">

                                    <div class=" layui-inline">
                                        <input type="number" class="placeholder input-group" name="number[]" value="'.$arr[0][$i++] .'">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <input type="number" step="0.1" class="placeholder input-group " name="discount[]" value="'.$arr[0][$i] .'">
                            </td>';
                            echo '</tr>';
                        }
                    }else{
                        $j = 1;
                        echo ' <tr>
                            <td>'.$j++ .'</td>
                            <td>
                                <div class="layui-input-item">

                                    <div class=" layui-inline">
                                        <input type="number" class="placeholder input-group" name="number[]">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <input type="number" step="0.01" class="placeholder input-group " name="discount[]">
                            </td>
                        </tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-9">
                <button  class="btn btn-primary" type="submit">确定</button>
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
        //新增一级优惠
        $("#addLevel").bind("click",addLevel);
    });
    var level = <?=$j?>;
    function addLevel() {
        let tr = document.createElement('tr')
        let h = `
            <td>${level}</td>
            <td>
            <div class="layui-input-item">
            <div class=" layui-inline">
            <input type="number" class="placeholder input-group" name="number[]">
            </div>
            </div>
            </td>
            <td>
            <input type="number" step="0.01" class="placeholder input-group " name="discount[]">
            </td>
            `
        tr.innerHTML = h;
        document.getElementById("discount").appendChild(tr)
        level+=1;
    }


</script>
<script src="/static/js/layui/layui.js"></script>
<script>
    layui.use('form', function(){
        var form = layui.form;
        //各种基于事件的操作，下面会有进一步介绍
    });

</script>

</body>
</html>