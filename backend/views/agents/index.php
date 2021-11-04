<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>代理商信息列表</title>
    <link rel="stylesheet" href="/static/js/layui/css/layui.css" media="all">
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
            <h5><strong>代理商信息列表</strong></h5>
        </div>
        <div class="ibox-content">

            <form class="layui-form"> <!-- 提示：如果你不想用form，你可以换成div等任何一个普通元素 -->

                <div class="layui-input-item">
                    <div class="layui-inline">
                        <?php if(\common\helpers\Tools::authCheck('agents/add')): ?>
                        <button type="button" id="add" class="btn btn-outline btn-primary ">添加代理商</button>
                        <?php endif;?>
                    </div>
                    <div class="layui-inline">
                        <?php if(\common\helpers\Tools::authCheck('agents/del')): ?>
                        <button type="button" id="delSelected" class="btn btn-outline btn-danger ">删除所选</button>
                        <?php endif;?>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-col-md5">
                            <select id="checkStatus" name="checkStatus" lay-filter="checkStatus" class="layui-col-md1" >
                                <option value="#">全部</option>
                                <option value="1">已审核</option>
                                <option value="0">未审核</option>
                            </select>
                        </div>
                    </div>
                    <div class=" layui-inline" style="margin-left: 150px">
                        <label >搜索方式:</label>
                    </div>
                    <div class=" layui-inline" >
                        <select id="type" name="type" lay-filter="type" >
                            <option value="contactName">联系人</option>
                            <option value="contactPhone">联系方式</option>
                        </select>
                    </div>
                    <div class="layui-inline">
                    <input type="text" value="" class="form-control" id="enter" name="enter" ">
                    </div>
                    <div class="layui-inline">
                    <button id="search" type="button" class="btn btn-primary" style="vertical-align: middle" <strong>搜索</strong></button>
                    </div>
                </div>
            </form>
            <!--搜索框结束-->
            <div class="example-wrap">
                <div class="example">
                        <table id="cusTable">
                            <thead>
                            <th data-field="checkbox" data-checkbox="true"></th>
<!--                            <th data-field="id">ID</th>-->
                            <th data-field="name">公司名称</th>
                            <th data-field="contactName">联系人</th>
                            <th data-field="contactPhone">联系方式</th>
                            <th data-field="region">所在地区</th>
                            <th data-field="addr">详细地址</th>
                            <th data-field="memo">备注</th>
                            <th data-field="status">状态</th>
                            <th data-field="operate">操作</th>
                            </thead>
                        </table>
                </div>
            </div>
        </div>
    </div>
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
            url: "/agents/index", //获取数据的地址
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
                    searchText:$('#enter').val(),
                    searchType:$('#type').val(),
                    checkStatus:$('#checkStatus').val()
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
        $("#search").bind("click",initTable);
        //删除
        $("#delSelected").bind("click",agentsDelSelected);
        //新增
        $("#add").bind("click",agentsAdd);
    });

    //审核
    function agentsCheck(id){
        $.getJSON("/agents/check", {'id' : id}, function(res){
            if(1 == res.code){
                layer.msg(res.msg);
                initTable();
            }else if(111 == res.code){
                window.location.reload();
            }else{
                layer.alert(res.msg, {title: '友情提示', icon: 2});
            }
        });
    }



    //代理商详情
    function agentsDet(id) {
        layer.open({
            type: 2,
            title: '代理商详情',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/agents/det?id=' + id
        });
    }


    //代理商价格编辑
    function agentsPrice(id) {
        layer.open({
            type: 2,
            title: '编辑代理商价格信息',
            shadeClose: true,
            shade: 0.2,
            area: ['60%', '90%'],
            content: '/agents/price?id=' + id
        });
    }

    //商品编辑
    function agentsEdit(id) {
        layer.open({
            type: 2,
            title: '编辑代理商信息',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/agents/edit?id=' + id
        });
    }

    function agentsDel(id){
        layer.confirm('确认删除此代理商?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/agents/del", {'id' : id}, function(res){
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
    function agentsDelSelected() {
        layer.confirm('确认删除此代理商?', {icon: 3, title:'提示'}, function(index) {
            var ids = "";
            var rows = $('#cusTable').bootstrapTable('getSelections');
            for (var i = 0; i < rows.length; i++) {
                ids +="'"+ rows[i].id + "'" + ',';
            }
            ids = ids.substring(0, ids.length - 1);
            //console.log(ids);

            $.getJSON("/agents/del-selected", {'ids' : ids}, function(res){
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
    //新增
    function agentsAdd() {
        layer.open({
            type: 2,
            title: '添加代理商',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/agents/add'
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
</body>
</html>