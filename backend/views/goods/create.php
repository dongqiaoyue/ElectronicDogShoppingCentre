<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <!-- 引入样式 -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="/static/goods/js/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <!-- Theme included stylesheets -->
    <!-- Core build with no theme, formatting, non-essential modules -->
    <link rel="stylesheet" href="/static/goods/css/index.css">
    <link href="/static/js/layui/css/layui.css" rel="stylesheet">
    <script type="text/javascript" charset="gbk" src="/static/goodsadd/js/ueditor.config.js"></script>
    <script type="text/javascript" charset="gbk" src="/static/goodsadd/js/ueditor.all.js"> </script >
    <script type="text/javascript" charset="utf-8" src="/static/goodsadd/lang/zh-cn/zh-cn.js"></script>
</head>
<body>
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
<div id="app">
    <div>
        <el-row>
            <el-col :span="12">
                <el-form ref="form" :model="form.Goods" label-width="80px">
                    <el-form-item label="名称">
                        <el-input v-model="form.Goods.title"></el-input>
                    </el-form-item>
                    <el-form-item label="简介">
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
                                <el-button plain type="primary" :disabled="uploading || picLoading || vidLoading || uploadingCover" @click="onSubmit">立即创建
                                </el-button>
                                <el-button>取消</el-button>
                                </el-form-item>
                                </el-form>
                                </el-col>
                                </el-row>
                                </div>
                                </div>
                                <script src="/static/js/jquery.min.js?v=2.1.4"></script>
                            <script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
                            <script src="/static/js/content.min.js?v=1.0.0"></script>
                            <script src="/static/js/plugins/layer/layer.min.js"></script>
                            <script src="/static/js/jquery.form.js"></script>
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
                                        vidFileList: [],
                                        skuFileList: [],
                                        nowSkuIndex: 0,
                                        skuLoadingList: [],
                                        skuTypes: []
                                    },
                                    methods: {
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
                                                    this.vidLoading = false;
                                                    return true;
                                                })
                                                .catch(err => {
                                                    this.vidLoading = false;
                                                    console.log(err);
                                                });
                                        },
                                        setAttribute(index) {
                                            document.querySelector(`#file${index}`).setAttribute('index', index);
                                        },
                                        onSubmit() {
                                            this.form.Goods.content = ue.getContent();
                                            let form = this.form;
                                            console.log(this.form);
                                            for (let i = 0; i < this.form.SkuInGoods.length; i++) {
                                                if (this.form.SkuInGoods[i] === undefined) {
                                                    this.form.SkuInGoods.splice(i, 1);
                                                    i--;   // 删除后当前位置变了，回退
                                                }
                                            }
                                            axios.post('http://m.huafuu.com/api/cargos/create', form)
                                                .then(res => {
                                                    console.log(res);
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
                                                    this.$message.error('上传信息出错');
                                                })
                                                .catch(err => {
                                                    console.log(err);
                                                });
                                        },
                                        submitUpload() {
                                            this.picLoading = true;
                                            let file = new FormData();
                                            let i = 0;
                                            for (let item of len) {
                                                file.append(i, this.picFileList[item.id].raw);
                                                i++;
                                            }
                                            axios.post('http://m.huafuu.com/api/cargos/upload', file)
                                                .then(res => {
                                                    console.log(res);
                                                    if (res.data.code === 200) {
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
                                            console.log(this.picFileList);
                                        },
                                        uploadPic() {
                                            return true;
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
                                        }
                                    },
                                    created() {
                                        this.addSku();
                                        axios.post('http://m.huafuu.com/api/cargos/create')
                                            .then(res => {
                                                console.log(res);
                                                this.skuTypes = res.data.data.sku;
                                            })
                                            .catch(err => {
                                                this.$message.error('sku获取失败');
                                            });
                                    }
                                });
                            </script>
                            <script src="/static/goods/js/drag.js"></script>
                            <script>
                                document.getElementsByClassName('el-upload__input')[1].addEventListener('change', function () {
                                    // let li = document.getElementsByClassName('el-upload-list--picture-card')[0].lastChild;
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
                            <!-- Initialize Quill editor -->
                            <script>
                                function getPersonInfo(one, two, three) {
                                    console.log(one);
                                    console.log(two);
                                    console.log(three);
                                }

                                const person = 'Lydia';
                                const age = 21;

                                getPersonInfo`${person} is ${age} years old`;
                            </script>

</body>
</html>
