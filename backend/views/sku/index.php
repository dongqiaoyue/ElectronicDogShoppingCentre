<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sku列表</title>
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
            <h5>sku列表</h5>
        </div>
        <div class="ibox-content">
            <div class="form-group clearfix col-sm-1">
                <?php if(\common\helpers\Tools::authCheck('sku/add')): ?>
                    <a href="javascript:skuAdd();">
                        <button class="btn btn-outline btn-primary" type="button">添加sku</button>
                    </a>
                <?php endif; ?>
            </div>
            <div  class="form-group clearfix col-sm-1">
                <?php if(\common\helpers\Tools::authCheck('sku/del')): ?>
                    <a href="javascript:skuDelSelected();">
                        <button type="button" id="delSelected" class="btn btn-outline btn-danger ">删除所选</button>
                    </a>
                <?php endif; ?>
            </div>
            <!--下拉框-->
            <div class="layui-inline">
                <div class="layui-col-md5">
                    <select id="checkStatus" name="checkStatus" lay-filter="checkStatus" class="layui-col-md1" >
                        <option value="#">全部</option>
                        <option value="1">已审核</option>
                        <option value="0">未审核</option>
                    </select>
                </div>
            </div>
            <!--搜索框开始-->
            <form id='commentForm' role="form" method="post" class="form-inline pull-right">
                <div class="content clearfix m-b">
                    <div class="form-group">
                        <label>sku名称：</label>
                        <input type="text" class="form-control" id="Name" name="Name">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="button" style="margin-top:5px" id="search"><strong>搜 索</strong>
                        </button>
                    </div>
                </div>
            </form>
            <!--搜索框结束-->
            <div class="example-wrap">
                <div class="example">
                    <table id="cusTable">
                        <thead>
                        <th data-field="checkbox" data-checkbox="true"></th>
                        <th data-field="ID">skuID</th>
                        <th data-field="Name">sku名称</th>
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
            url: "/sku/index", //获取数据的地址
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
                    searchText:$('#Name').val()
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
    });

    //批量删除
    function skuDelSelected() {
        layer.confirm('确认删除这些sku?', {icon: 3, title:'提示'}, function(index) {
            var ids = "";
            var rows = $('#cusTable').bootstrapTable('getSelections');
            for (var i = 0; i < rows.length; i++) {
                ids +="'"+ rows[i].ID + "'" + ',';
            }
            ids = ids.substring(0, ids.length - 1);
            // console.log(rows);

            $.getJSON("/sku/del-selected", {'ids' : ids}, function(res){
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

    function skuAdd() {
        layer.open({
            type: 2,
            title: '添加sku',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '45%'],
            content: '/sku/add'
        });
    }

    function skuEdit(id) {
        layer.open({
            type: 2,
            title: '编辑sku',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/sku/edit?id=' + id
        });
    }

    function skuBelow(id){
        layer.open({
            type: 2,
            title: id,
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/sku/below'
        });
    }

    function skuDel(id){
        layer.confirm('确认删除此sku?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/sku/del", {'id' : id}, function(res){
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
</script>
</body>
</html>