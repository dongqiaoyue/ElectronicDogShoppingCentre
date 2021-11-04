<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单列表</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Panel Other -->
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>订单列表</h5>
        </div>
        <div class="ibox-content">
            <!--搜索框开始-->
            <form class="layui-form"> <!-- 提示：如果你不想用form，你可以换成div等任何一个普通元素 -->
                <div class="layui-input-item">
                    <div class=" layui-inline">
                        <label >按日期范围:</label>
                    </div>
                    <div class="layui-inline">
                        <input type="text" class="layui-input" name="date" id="test1">
                    </div>
                    <div class=" layui-inline">
                        <label >按地区范围:</label>
                    </div>
                    <div class=" layui-inline" >
                        <select id="region" name="region" lay-filter="type" >
                            <option value="#">请选择</option>
                            <?php
                            foreach ($province as $key => $value){
                                echo '<option value = '. $value['Id'] .'>'.$value['Name'] .' ';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="layui-inline">
                        <button id="search" type="button" class="btn btn-primary" style="vertical-align: middle" <strong>搜索</strong></button>
                    </div>
                    <div class="layui-inline ">
                        <?php if(\common\helpers\Tools::authCheck('data/agent-excel')): ?>
                        <a href="javascript:output();">
                            <button id="output" type="button" class="btn btn-info" style="vertical-align: middle" <strong>导出excel</strong></button>
                        </a>
                        <?php endif;?>
                    </div>
                    <div class="layui-inline ">
                        <?php if(\common\helpers\Tools::authCheck('data/agent-chart')): ?>
                        <a href="javascript:chart();">
                            <button id="chart" type="button" class="btn btn-success" style="vertical-align: middle" <strong>查看图表</strong></button>
                        </a>
                        <?php endif;?>
                    </div>
                </div>
                <br>
            </form>
            <!--搜索框结束-->
            <div class="example-wrap">
                <div class="example">
                    <table id="cusTable">
                        <thead>
                        <th data-field="ID">订单号</th>
                        <th data-field="agentAddr">收货地址</th>
                        <th data-field="totalMoney">总价</th>
                        <th data-field="addAt">创建时间</th>
<!--                        <th data-field="updateAt">更新时间</th>-->
                        <th data-field="status">状态</th>
                        <th data-field="operate">操作</th>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- End Example Pagination -->
        </div>
    </div>
</div>
<!-- End Panel Other -->
</div>
<script src="/static/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/js/content.min.js?v=1.0.0"></script>
<script src="/static/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/static/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
<script src="/static/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/static/js/plugins/layer/laydate/laydate.js"></script>
<script src="/static/js/plugins/layer/layer.min.js"></script>
<script type="text/javascript">
    function initTable() {
        //先销毁表格
        $('#cusTable').bootstrapTable('destroy');
        //初始化表格,动态从服务器加载数据
        $("#cusTable").bootstrapTable({
            method: "get",  //使用get请求到服务器获取数据
            url: "/data/agent-orders", //获取数据的地址
            striped: true,  //表格显示条纹
            pagination: true, //启动分页
            pageSize: 10,  //每页显示的记录数
            pageNumber:1, //当前第几页
            pageList: [5, 10, 15, 20, 25],  //记录数可选列表
            sidePagination: "server", //表示服务端请求
            paginationFirstText: "首页",
            paginationPreText: "上一页",
            paginationNextText: "下一页",
            paginationLastText: "尾页",
            queryParamsType : "undefined",
            queryParams: function queryParams(params) {   //设置查询参数
                var param = {
                    pageNumber: params.pageNumber,
                    pageSize: params.pageSize,
                    date:$('#test1').val(),
                    region:$('#region').val()
                };
                return param;
            },
            onLoadSuccess: function(res){  //加载成功时执行
                if(111 == res.code){
                    window.location.reload();
                }
                // layer.msg("加载成功", {time : 1000});
            },
            onLoadError: function(){  //加载失败时执行
                layer.msg("加载数据失败");
            }
        });
    }

    $(document).ready(function () {
        //调用函数，初始化表格
        initTable();

        //当点击查询按钮的时候执行
        $("#search").bind("click", initTable);
        //导出
        //$("#output").bind("click",output);
        //查看图表
        //$("#chart").bind("click",chart);
    });
    //导出
    function output() {
        // console.log($('#test1').val());
        // console.log($('#region').val());
        window.location.href='<?=\yii\helpers\Url::toRoute("data/agent-excel")?>'
            +'?date='+$('#test1').val()+'&region='+$('#region').val();
    }
    //图表
    function  chart() {
        layer.open({
            type: 2,
            title: '图表',
            shadeClose: true,
            shade: 0.2,
            area: ['99%', '99%'],
            content: '/data/agent-chart?date='+ $('#test1').val()
        });
    }
    
    

    //订单详情
    function ordersDet(id) {
        layer.open({
            type: 2,
            title: '订单详情',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/agent-orders/det?id=' + id
        });
    }

    function ordersDel(id){
        layer.confirm('确认删除此订单?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/agent-orders/del", {'id' : id}, function(res){
                if(1 == res.code){
                    layer.msg(res.msg);
                    setTimeout(function(){
                        initTable();
                    }, 1000);
                }else if(111 == res.code){
                    window.location.reload();
                }else{
                    layer.alert(res.msg, {title: '友情提示', icon: 2});
                }
            });

            layer.close(index);
        })
    }

    //批量删除
    function ordersDelSelected() {
        layer.confirm('确认删除所选投诉信息?', {icon: 3, title:'提示'}, function(index) {
            var ids = "";
            var rows = $('#cusTable').bootstrapTable('getSelections');
            for (var i = 0; i < rows.length; i++) {
                ids +="'"+ rows[i].ID + "'" + ',';
            }
            ids = ids.substring(0, ids.length - 1);
            //console.log(ids);

            $.getJSON("/agent-orders/del-selected", {'ids' : ids}, function(res){
                if(1 == res.code){
                    layer.msg(res.msg);
                    setTimeout(function(){
                        initTable();
                    }, 1000);
                }else if(111 == res.code){
                    window.location.reload();
                }else{
                    layer.alert(res.msg, {title: '友情提示', icon: 2});
                }
            });
            layer.close(index);
        });
    }
</script>

<script src="/static/js/layui/layui.js"></script>
<script>
    layui.use('form', function(){
        var form = layui.form;
        //各种基于事件的操作，下面会有进一步介绍
        form.on('select(checkStatus)',function (data) {
            initTable();
        })

    });
</script>
<script>
    layui.use('laydate', function(){
        var laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            elem: '#test1'
            ,range: true//指定元素
        });
    });
</script>
</body>
</html>