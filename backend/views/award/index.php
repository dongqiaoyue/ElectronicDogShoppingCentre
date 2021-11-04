<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>有奖建议列表</title>
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
            <h5>有奖建议列表</h5>
        </div>
        <div class="ibox-content">

            <!--搜索框开始-->
            <form class="layui-form"> <!-- 提示：如果你不想用form，你可以换成div等任何一个普通元素 -->

                <div class="layui-input-item">
                    <div class="layui-inline">
                        <?php if(\common\helpers\Tools::authCheck('award/del-selected')): ?>
                        <a href="javascript:awardsDelSelected();">
                            <button type="button" id="delSelected" class="btn btn-outline btn-danger ">删除所选</button>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-col-md5">
                            <select id="checkStatus" name="checkStatus" lay-filter="checkStatus" class="layui-col-md1" >
                                <option value="#">全部</option>
                                <option value="1">已处理</option>
                                <option value="0">未处理</option>
                            </select>
                        </div>
                    </div>
                    <div class=" layui-inline" style="margin-left: 150px">
                        <label >搜索方式:</label>
                    </div>
                    <div class=" layui-inline" >
                        <select id="type" name="type" lay-filter="type" >
                            <option value="name">联系人</option>
                            <option value="phone">联系方式</option>
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
                        <!--                        <th data-field="id">ID</th>-->
                        <th data-field="name">联系人</th>
                        <th data-field="phone">联系方式</th>
                        <th data-field="addAt">创建时间</th>
                        <th data-field="content">建议内容</th>
                        <th data-field="status">状态</th>
                        <th data-field="result">处理结果</th>
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
            url: "/award/index", //获取数据的地址
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
        $("#search").bind("click", initTable);
        //删除
        //$("#delSelected").bind("click",awardsDelSelected);
    });
    //投诉详情
    function awardDet(id) {
        layer.open({
            type: 2,
            title: '建议详情',
            shadeClose: true,
            shade: 0.2,
            area: ['80%', '90%'],
            content: '/award/det?id=' + id
        });
    }
    //删除
    function awardDel(id){
        layer.confirm('确认删除此条建议吗?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("/award/del", {'id' : id}, function(res){
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
    function awardsDelSelected() {
        layer.confirm('确认删除所选建议信息?', {icon: 3, title:'提示'}, function(index) {
            var ids = "";
            var rows = $('#cusTable').bootstrapTable('getSelections');
            for (var i = 0; i < rows.length; i++) {
                ids +="'"+ rows[i].id + "'" + ',';
            }
            ids = ids.substring(0, ids.length - 1);
            //console.log(ids);

            $.getJSON("/award/del-selected", {'ids' : ids}, function(res){
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

    //处理信息
    function awardDeal(id){
        layer.open({
            type: 2,
            title: '处理结果',
            shadeClose: true,
            shade: 0.2,
            area: ['70%', '80%'],
            content: '/award/deal?id=' + id
        });
        // $.getJSON("/complaint/deal", {'id' : id}, function(res){
        //     if(1 == res.code){
        //         layer.msg(res.msg);
        //         initTable();
        //     }else if(111 == res.code){
        //         window.location.reload();
        //     }else{
        //         layer.alert(res.msg, {title: '友情提示', icon: 2});
        //     }
        // });
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