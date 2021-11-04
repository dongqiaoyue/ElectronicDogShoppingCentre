<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <!-- 引入样式 -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="/static/goodsadd/js/vue.js"></script>
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.3.0/video-js.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.3.0/video.min.js"></script>

    <script type="text/javascript" charset="gbk" src="/static/goodsadd/js/ueditor.config.js"></script>
    <script type="text/javascript" charset="gbk" src="/static/goodsadd/js/ueditor.all.js"> </script >
    <script type="text/javascript" charset="utf-8" src="/static/goodsadd/lang/zh-cn/zh-cn.js"></script>
    <script src="/static/js/jquery.min.js?v=2.1.4"></script>

    <link rel="stylesheet" href="/static/goodsadd/css/index.css">
</head>
<body>
<div id="app">
    <div>
        <el-row>
            <el-col :span="12">
                <el-form ref="form" :model="form.Goods" label-width="80px">
                    <el-form-item label="名称">
                        <el-input v-model="form.Goods.title"></el-input>
                    </el-form-item>
                    <input id="admin" style="display: none;" value="<?=Yii::$app->session->get('admin_id');?>"></input>
                    <el-form-item label="简介" class="quill">
                        <div>
                            <script id="editor" type="text/plain" style="width: 100%;height: auto"></script>
                            </div>
                            </el-form-item>
                            <el-form-item label="备注">
                                <el-input type="textarea" v-model="form.Goods.memo"></el-input>
                                </el-form-item>
                                <el-form-item label="排序">
                                <el-input v-model="form.Goods.sort"></el-input>
                                </el-form-item>
                                <el-form-item label="封面">
                                <el-upload
                            :limit="1"
                            action="123"
                            :http-request="uploadCov"
                            :file-list="covFileList"
                            list-type="picture-card">
                                <i class="el-icon-plus"></i>
                                </el-upload>
                                </el-form-item>
                                <el-form-item label="图片">
                                <el-upload
                            multiple
                            ref="pciUpload"
                            :auto-upload="false"
                            action="123"
                            :http-request="uploadPic"
                            :on-change="onChange"
                            :on-remove="onRemove"
                            :file-list="picFileList"
                            list-type="picture-card">
                                <i class="el-icon-plus"></i>
                                </el-upload>
                                <el-button v-loading="picLoading" plain size="large" type="success" @click="submitUpload">上传图片到服务器
                                <i class="el-icon-picture-outline el-icon--right"></i>
                                </el-button>
                                </el-form-item>
                                <el-form-item label="视频">
                                <el-upload
                            class="upload-demo"
                            action="123"
                            :file-list="vidFileList"
                            :before-upload="beforeUploadVid"
                            :http-request="uploadVideo">
                                <el-button v-loading="vidLoading" plain slot="trigger" size="large" type="success">上传视频文件
                                <i class="el-icon-video-camera-solid el-icon--right"></i></el-button>
                            </el-upload>
                            </el-form-item>
                            <el-form-item label="视频地址">
                                <el-input v-model="form.GoodsVid"></el-input>
                                </el-form-item>
                                <el-form-item>
                                <video id="example_video_1" class="video-js vjs-default-skin" controls preload="none" width="640"
                            height="264">
                                <source class="vid" src="" type="video/mp4">
                                </video>
                                </el-form-item>
                                <el-form-item label="sku">
                                <el-button plain type="primary" size="large" @click="addSku">新增</el-button>
                                </el-form-item>
                                <el-form-item>
                                <div class="skuContent">
                                <el-row class="sku" justify="space-between" type="flex" v-if="item"
                                v-for="(item, index) in form.SkuInGoods"
                                :key="index">
                                <el-col :span="9">
                                <el-input class="skuItem" v-model="item.price" placeholder="价格"></el-input>
                                <el-input class="skuItem" v-model="item.inventory" placeholder="库存"></el-input>
                                <el-select class="skuItem" style="width: 100%" v-model="item.skuID" placeholder="颜色">
                                <el-option :label="item.Name" :value="item.skuID" v-for="(item, index) in skuTypes"
                                :key="index"></el-option>
                                </el-select>
                                </el-col>
                                <el-col :span="12">
                                <el-upload
                            action="123"
                            :limit="1"
                            :before-upload="beforeUploadSkuPic"
                            :http-request="uploadSkuPic"
                            :file-list="item.pic"
                            list-type="picture">
                                <el-button size="large" :disabled="uploading" plain type="success" @click="saveIndex(index)">
                                点击上传sku图片<i
                            class="el-icon-picture el-icon--right"></i></el-button>
                            </el-upload>
                            </el-col>
                            <el-col :span="2" style="text-align: right">
                                <el-button plain type="danger" size="mini" icon="el-icon-close" circle
                            @click="delSku(item, index)"></el-button>
                                </el-col>
                                </el-row>
                                </div>
                                </el-form-item>
                                <el-form-item>
                                <el-button plain type="primary" :disabled="uploading || picLoading || vidLoading" @click="ifEdit(true)">
                                保存修改
                                </el-button>
                                <el-button>取消</el-button>
                                </el-form-item>
                                </el-form>
                                </el-col>
                                </el-row>
                                </div>
                                </div>
                                <script type="text/javascript">

                                <script>
                            var cont = '';
                            var dom1 = '';
                            var GoodsId = '';
                            // window.onload = function () {
                            //     $.ajax({
                            //         type: 'GET',
                            //         url: 'http://m.huafuu.com/api/cargos/tree',
                            //         dataType: "JSON",
                            //         success: function (value) {
                            //             console.log("number: "+JSON.stringify(value.data.data));
                            //             var ifr = 'iframe'+value.data.data;
                            //             var cont = window.top.frames[ifr].document;
                            //             var dom1 = cont.querySelector('.layui-layer-title');
                            //             console.log("innerHTML:1  "+dom1.querySelector('#title').innerHTML);
                            //             var GoodsId = dom1.querySelector('#title').innerHTML;
                            //             console.log("innerHTML:2  "+dom1.querySelector('#title').innerHTML);
                            //
                            //         },
                            //         error:function() {
                            //             console.log("error");
                            //         }
                            //     })
                            // }
                            </script>
                            <script type="text/javascript">
                                var ue = UE.getEditor('editor', {
                                    autoHeightEnabled: true,
                                    autoFloatEnabled: true,
                                    initialFrameWidth: 1000,
                                    initialFrameHeight: 483,
                                    toolbars: [[
                                        'source', '|', 'undo', 'redo', '|',
                                        'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                                        'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                                        'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                                        'directionalityltr', 'directionalityrtl', 'indent', '|',
                                        'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                                        'link', 'unlink', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                                        'simpleupload', 'insertimage', 'attachment', 'insertcode', 'pagebreak', 'template', 'background', '|',
                                        'horizontal', '|',
                                        'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                                        'print', 'preview', 'searchreplace'
                                    ]]
                                });
                            </script>
                            <script>
                                new Vue({
                                    el: '#app',
                                    data: {
                                        uploadingCover: false,
                                        uploading: false,
                                        picLoading: false,
                                        vidLoading: false,
                                        form: {
                                            Goods: {
                                                title: '',
                                                content: '',
                                                memo: '',
                                                sort: ''
                                            },
                                            GoodsCov: {},
                                            SkuInGoods: [],
                                            GoodsImg: [],
                                            GoodsVid: '',
                                        },
                                        skuForm: {
                                            price: '',
                                            inventory: '',
                                            skuID: '',
                                            images: ''
                                        },
                                        picFileList: [],
                                        covFileList: [],
                                        vidFileList: [],
                                        skuFileList: [],
                                        nowSkuIndex: 0,
                                        skuLoadingList: [],
                                        skuTypes: []
                                    },
                                    methods: {
                                        saveIndex(index) {
                                            this.nowSkuIndex = index;
                                        },
                                        beforeUploadVid(file) {
                                            console.log(file);
                                            if (['video/mp4', 'video/ogg', 'video/flv', 'video/avi', 'video/wmv', 'video/rmvb'].indexOf(file.type) == -1) {
                                                this.$message.error('请上传正确的视频格式');
                                                return false;
                                            }
                                            this.vidLoading = true;
                                            let File = new FormData();
                                            File.append('0', file);
                                            axios.post('http://m.huafuu.com/api/cargos/upload', File)
                                                .then(res => {
                                                    console.log(res);
                                                    if (res.data.code === 500) {
                                                        this.$message.error('上传失败');
                                                        this.vidLoading = false;
                                                        return false;
                                                    }
                                                    this.$message.success('上传成功');
                                                    this.form.GoodsVid = res.data.data.path[0].url;
                                                    document.getElementsByClassName('vid')[0].src = `/api${res.data.data.path[0].url}`;
                                                    this.vidLoading = false;
                                                    return true;
                                                })
                                                .catch(err => {
                                                    console.log(err);
                                                });
                                        },
                                        setAttribute(index) {
                                            document.querySelector(`#file${index}`).setAttribute('index', index);
                                        },
                                        submitUpload() {
                                            this.picLoading = true;
                                            let file = new FormData();
                                            let i = 0;
                                            console.log(len);
                                            console.log(this.picFileList);
                                            for (let item of len) {
                                                file.append(i, this.picFileList[item.id].raw);
                                                i++;
                                            }
                                            axios.post('http://m.huafuu.com/api/cargos/upload', file)
                                                .then(res => {
                                                    console.log(res);
                                                    if (res.data.data.code === 1) {
                                                        this.$message.success('上传成功');
                                                        this.form.GoodsImg = res.data.data.path;
                                                    } else this.$message.success('上传失败');
                                                    this.picLoading = false;
                                                })
                                                .catch(err => {
                                                    console.log(err);
                                                });
                                        },
                                        onChange(file, fileList) {
                                            this.picFileList = fileList;
                                            console.log(this.picFileList);
                                        },
                                        onRemove(file, fileList) {
                                            // this.picFileList = fileList;
                                            console.log(this.picFileList);
                                        },
                                        uploadPic() {
                                            return true;
                                        },
                                        async uploadCov(data) {
                                            try {
                                                if (['image/jpeg', 'image/x-portable-bitmap', 'image/bmp', 'image/png'].indexOf(data.file.type) == -1) {
                                                    this.$message.error('请上传正确的图片格式');
                                                    return false;
                                                }
                                                this.uploadingCover = true;
                                                let File = new FormData();
                                                File.append('0', data.file);
                                                axios.post('http://m.huafuu.com/api/cargos/upload', File)
                                                    .then(res => {
                                                        console.log(res);
                                                        if (res.data.code === 500) {
                                                            this.$message.error('上传失败');
                                                            return false;
                                                        }
                                                        this.$message.success('上传成功');
                                                        this.form.GoodsCov.url = res.data.data.path[0].url;
                                                        return true;
                                                    })
                                                    .catch(err => {
                                                        console.log(err);
                                                    })
                                                    .finally(() => {
                                                        this.uploadingCover = false;
                                                    })
                                            }
                                            catch (e) {
                                                console.log(e);
                                            }
                                        },
                                        uploadVideo(file) {
                                        },
                                        uploadSkuPic(file) {
                                        },
                                        beforeUploadSkuPic(file) {
                                            console.log(file);
                                            if (['image/jpeg', 'image/x-portable-bitmap', 'image/bmp', 'image/png'].indexOf(file.type) == -1) {
                                                this.$message.error('请上传正确的图片格式');
                                                return false;
                                            }
                                            this.uploading = true;
                                            let File = new FormData();
                                            File.append('0', file);
                                            axios.post('http://m.huafuu.com/api/cargos/upload', File)
                                                .then(res => {
                                                    console.log(res);
                                                    if (res.data.code === 500) {
                                                        this.$message.error('上传失败');
                                                        this.uploading = true;
                                                        return false;
                                                    }
                                                    this.uploading = false;
                                                    this.$message.success('上传成功');
                                                    this.form.SkuInGoods[this.nowSkuIndex].images = res.data.data.path[0].url;
                                                    return true;
                                                })
                                                .catch(err => {
                                                    console.log(err);
                                                });
                                        },
                                        addSku() {
                                            this.form.SkuInGoods.push(Object.assign({}, this.skuForm));
                                        },
                                        delSku(item, index) {
                                            console.log(item);
                                            this.form.SkuInGoods.splice(index, 1, undefined);
                                        },
                                        async getSku() {
                                            axios.post('http://m.huafuu.com/api/cargos/create')
                                                .then(res => {
                                                    console.log(res);
                                                    this.skuTypes = res.data.data.sku;
                                                })
                                                .catch(err => {
                                                    this.$message.error('sku获取失败');
                                                });
                                        },
                                        async ifEdit(ifSubmit) {
                                            try {
                                                if (!ifSubmit) {
                                                    let adminID = document.getElementById("admin").value;
                                                    // alert(adminID);
                                                    let GoodsIdData = await axios.get('http://m.huafuu.com/api/cargos/tree?id='+adminID);
                                                    console.log( window.top.frames[`iframe${GoodsIdData.data.data.data}`],GoodsIdData.data.data.data);
                                                    cont = window.top.frames[`iframe${GoodsIdData.data.data.data}`].document;
                                                    dom1 = cont.querySelector('.layui-layer-title');
                                                    GoodsId = dom1.querySelector('#title').innerHTML;
                                                    let res = await axios.post(`http://m.huafuu.com/api/cargos/edit?id=${GoodsId}`);
                                                    const data = res.data.data;
                                                    document.getElementsByClassName('vid')[0].src = `/api${data.goodsVid.url}`;
                                                    this.form.Goods = data.goods;
                                                    this.form.GoodsCov.url = data.goodsCov.url;
                                                    this.form.GoodsImg = data.goodsImg;
                                                    this.form.GoodsVid = data.goodsVid.url;
                                                    this.form.SkuInGoods = data.skuGoods;
                                                    this.form.SkuInGoods.map(item => {
                                                        let obj = {
                                                            name: item.images.slice(item.images.length - 3, item.images.length),
                                                            url: 'http://m.huafuu.com' + item.images
                                                        };
                                                        item.pic = [obj];
                                                    });
                                                    this.covFileList = [{
                                                        name: data.goodsCov.url.slice(data.goodsCov.url - 3, data.goodsCov.url.length),
                                                        url: 'http://m.huafuu.com' + data.goodsCov.url
                                                    }];
                                                    this.picFileList = data.goodsImg.map(item => {
                                                        let obj = {
                                                            name: item.id,
                                                            url: 'http://m.huafuu.com' + item.url
                                                        };
                                                        return obj;
                                                    });
                                                    this.picFileList.map(async item => {
                                                        let data = await axios.get(`${item.url}`, {responseType: 'arraybuffer'});
                                                        let j = item.url.slice(item.url.length - 3, item.url.length);
                                                        let i = item.url.substr(item.url.lastIndexOf("/") + 1);
                                                        // let blob = window.URL.createObjectURL(data.data);
                                                        item.raw = new File([data.data], i, {
                                                            type: 'image/' + j
                                                        });
                                                    });
                                                    console.log(this.picFileList);
                                                    ue.ready(function () {
                                                        ue.setContent(data.goods.content, true);
                                                    });
                                                    return;
                                                }
                                                console.log(this.form);
                                                this.form.Goods.content = ue.getContent();
                                                delete this.form.Goods.status;
                                                delete this.form.Goods.id;
                                                delete this.form.Goods._links;
                                                this.form.GoodsImg.map(item => {
                                                    delete item.type;
                                                    delete item.id;
                                                });
                                                for (let i = 0; i < this.form.SkuInGoods.length; i++) {
                                                    if (this.form.SkuInGoods[i] === undefined) {
                                                        this.form.SkuInGoods.splice(i, 1);
                                                        i--;   // 删除后当前位置变了，回退
                                                    } else {
                                                        delete this.form.SkuInGoods[i].id;
                                                        delete this.form.SkuInGoods[i].pic;
                                                    }
                                                }
                                                console.log(this.form);
                                                let res = await axios.post(`http://m.huafuu.com/api/cargos/edit?id=${GoodsId}`, this.form);
                                                console.log(res.data.code);
                                                if (res.data.code === 200) {
                                                    this.$message.success('上传信息成功');
                                                    //window.parent.location.reload();

                                                    window.parent.initTable();

                                                    setTimeout(function () {
                                                        var p = parent.layer.getFrameIndex(window.name);
                                                        parent.layer.close(p);
                                                    }, 1000);
                                                    return;
                                                }
                                                console.log(res);
                                            } catch (e) {
                                                console.log(e);
                                            }
                                        }
                                    },
                                    created() {
                                        this.addSku();
                                        this.getSku();
                                        this.ifEdit();
                                    },
                                    beforeUpdate() {
                                        this.$nextTick(function () {
                                            window.len = document.getElementsByClassName('el-upload-list--picture-card')[1].getElementsByTagName('li');
                                            window.i = 0;
                                            for (let item of len) {
                                                if (!item.attributes.index) {
                                                    item.setAttribute('draggable', 'true');
                                                    item.setAttribute('id', i);
                                                    item.setAttribute('ondragstart', 'drag(event)');
                                                    item.setAttribute('ondrop', 'onDrop(event)');
                                                    item.setAttribute('ondragover', 'onDragOver(event)');
                                                }
                                                item.setAttribute('index', i);
                                                i++;
                                            }
                                        });
                                    }
                                });
                            </script>
                            <script>
                                document.getElementsByClassName('el-upload__input')[0].addEventListener('change', function () {
                                    len = document.getElementsByClassName('el-upload-list--picture-card')[1].getElementsByTagName('li');
                                    i = 0;
                                    for (let item of len) {
                                        if (!item.attributes.index) {
                                            item.setAttribute('draggable', 'true');
                                            item.setAttribute('id', i);
                                            item.setAttribute('ondragstart', 'drag(event)');
                                            item.setAttribute('ondrop', 'onDrop(event)');
                                            item.setAttribute('ondragover', 'onDragOver(event)');
                                        }
                                        item.setAttribute('index', i);
                                        i++;
                                    }
                                });
                            </script>
                            <!--<script>-->
                            <!--  console.log('123', window.top.frames['iframe8'].document);-->
                            <!--  var cont = window.top.frames['iframe8'].document;-->
                            <!--  var id = cont.querySelector('.layui-layer-title').innerHTML;-->
                            <!--</script>-->
                            <script src="/static/goodsadd/js/drag.js"></script>
                            <script>
                                var player = videojs('example_video_1', {
                                    muted: true,
                                    controls: true / false,
                                    height: 400,
                                    width: 600,
                                    loop: true,
                                    // 更多配置.....
                                });
                            </script>
</body>
</html>
