<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新闻列表</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/css/animate.min.css" rel="stylesheet">
    <link href="/static/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Panel Other -->
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>基础信息列表</h5>
        </div>
        <div class="ibox-content">
            <div class="form-group clearfix col-sm-1" style="margin-right: 25px;">
                <?php if(\common\helpers\Tools::authCheck('cinfo/add')): ?>
                    <a href="javascript:cinfoAdd();">
                        <button class="btn btn-outline btn-primary" type="button">添加基础信息</button>
                    </a>
                <?php endif; ?>
            </div>
            <div  class="form-group clearfix col-sm-1">
                <?php if(\common\helpers\Tools::authCheck('cinfo/del')): ?>
                    <a href="javascript:cinfoDelSelected();">
                        <button type="button" id="delSelected" class="btn btn-outline btn-danger ">删除所选</button>
                    </a>
                <?php endif; ?>
            </div>
            <div  class="form-group clearfix col-sm-1">
                <?php if(\common\helpers\Tools::authCheck('cinfo/addPrice')): ?>
                    <a href="javascript:cinfoAddPrice();">
                        <button type="button" id="addPrice" class="btn btn-outline btn-danger ">添加有奖图片</button>
                    </a>
                <?php endif; ?>
            </div>
            <!--搜索框开始-->
            <form id='commentForm' role="form" method="post" class="form-inline pull-right">
                <div class="content clearfix m-b">
                    <div class="form-group">
                        <label>文本标题：</label>
                        <input type="text" class="form-control" id="header" name="title">
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
                        <th data-field="status">信息类别</th>
                        <th data-field="title">信息标题</th>
                        <th data-field="addAt">添加时间</th>
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
<script type="text/javascript">
    function initTable() {
        //先销毁表格
        $('#cusTable').bootstrapTable('destroy');
        //初始化表格,动态从服务器加载数据
        $("#cusTable").bootstrapTable({
            method: "get",  //使用get请求到服务器获取数据
            url: "/cinfo/index", //获取数据的地址
            striped: true,  //表格显示条纹
            pagination: true, //启动分页
            pageSize: 10,  //每页显示的记录数
            pageNumber:1, //当前第几页
            pageList: [5, 10, 15, 20, 25],  //记录数可选列表
            sidePagination: "server", //表示服务端请求  若"client"则不显示数据
            paginationFirstText: "首页",
            paginationPreText: "上一页",
            paginationNextText: "下一页",
            paginationLastText: "尾页",
            queryParamsType : "undefined",
            queryParams: function queryParams(params) {   //设置查询参数
                var param = {
                    pageNumber: params.pageNumber,
                    pageSize: params.pageSize,
                    searchText:$('#header').val()
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
    function cinfoDelSelected() {
        layer.confirm('确认删除这些信息?', {icon: 3, title:'提示'}, function(index) {
            var ids = "";
            var rows = $('#cusTable').bootstrapTable('getSelections');
            for (var i = 0; i < rows.length; i++) {
                ids +="'"+ rows[i].id + "'" + ',';
            }
            ids = ids.substring(0, ids.length - 1);
            //console.log(ids);

            $.getJSON("/cinfo/del-selected", {'ids' : ids}, function(res){
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

    function cinfoAdd() {
        layer.open({
            type: 2,
            title: '添加基础信息',
            shadeClose: false,
            shade: 0.2,
            area: ['100%', '100%'],
            content: '/cinfo/add',
        });
    }

    function cinfoEdit(id) {
        layer.open({
            type: 2,
            title: '编辑基础信息',
            shadeClose: false,
            shade: 0.2,
            area: ['100%', '100%'],
            content: '/cinfo/edit?id=' + id
        });
    }

    function cinfoDet(id){
        layer.open({
            type: 2,
            title: '基础信息详情',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/cinfo/det?id=' + id + '&show=1'
        });
    }

    function cinfoAddPrice(id){
        layer.open({
            type: 2,
            title: '添加有奖建议图片',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '60%'],
            content: '/cinfo/add-price?id=' + id + '&show=1'
        });
    }

    function cinfoCover(id){
        layer.open({
            type: 2,
            title: '封面',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/cinfo/det?id=' + id + '&show=2'
        });
    }

    function cinfoDel(id){
        layer.confirm('确认删除此信息?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/cinfo/del", {'id' : id}, function(res){
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
<script src="/static/js/plugins/layer/layer.min.js"></script>
</body>
</html>