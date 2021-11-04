<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/7/21
 * Time: 16:07
 */
namespace backend\controllers;

use backend\models\Admins;
use backend\models\Dictionary;
use backend\models\Info;
use common\helpers\Tools;
use yii\web\Response;
use yii\web\UploadedFile;
use yii;

class CinfoController extends BaseController
{
    public $enableCsrfValidation = false;

    public function actions(){
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "http://test.cuittk.cn", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/upload/cover/{yyyy}{mm}{dd}/{time}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            if (!empty($param['searchText'])) {
                $where = ['like', 'title', $param['searchText']];
            }

            $selectResult = info::getInfoByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){
                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);
                $selectResult[$key]['status'] = Info::getStatusBystus($selectResult[$key]['status']);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['id']));
            }

            $return['total'] = Info::getInfoNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    public function actionAdd()
    {
        $cinfo = new Info();
        $request = \Yii::$app->request;

        if($request->isPost){
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

//            $info = new Info();
            $res = $cinfo->addInfo($param['Info']);

            return $res;
        }

        $dicts = Dictionary::getDictByPN('基础信息');
        return $this->render('add', [
            'cinfo' => $cinfo,
            'dicts' => $dicts
        ]);
    }

    /**
     * 封面上传 成功则返回一个存储路径
     * @return string
     */
    public function actionCov()
    {
        $request = \Yii::$app->request;

        if($request->isPost){
            // 检测节点名称的唯一性
            $has = info::find()->select(['id'])->where(['cover' => $_FILES['file']['name']])->one();

            if(!empty($has)){
                $res =  ['code' => -2, 'data' => '', 'msg' => '该图片已经上传或同名'];
            }else{
                //判断是否为更新图片
                if($request->get('del') == '1'){
                    $cover = Info::getInfoById($request->get('id'))['cover'];
                    //编辑的时候还没有图片
                    if(!empty($cover)){
                        $file = $_SERVER['DOCUMENT_ROOT'].$cover;

                        $res = Tools::file_delete($file);
                    }
                }
                //获取web根目录
                $docroot = "/upload/cover/";
                $res = Tools::file_upload($docroot);
            }

            return Tools::json($res);
        }
    }

    /**
     * 视频上传 成功则返回一个存储路径
     * @return string
     */
    public function actionVid()
    {
        $request = \Yii::$app->request;

        if($request->isPost){
            // 检测节点名称的唯一性
            $has = info::find()->select(['id'])->where(['attach' => $_FILES['file']['name']])->one();

            if(!empty($has)){
                $res =  ['code' => -2, 'data' => '', 'msg' => '该视频已经上传或同名'];
            }else{
                //获取web根目录
                $docroot = "/upload/video/";
                $res = Tools::file_upload($docroot);
            }

            return Tools::json($res);
        }
    }

    /**
     * 多视频上传 成功则返回一个存储路径
     * @return string
     */
    public function actionMulVid()
    {
        $request = \Yii::$app->request;

        if($request->isPost){
            // 检测节点名称的唯一性
            $has = info::find()->select(['id'])->where(['attach' => $_FILES['file']['name']])->one();

            if(!empty($has)){
                $res =  ['code' => -2, 'data' => '', 'msg' => '该视频已经上传或同名'];
            }else{
                //获取web根目录
                $docroot = "/upload/video/";
                $res = Tools::file_upload($docroot);
            }
            $type = $request->get('type');
            $id = $request->get('id');
            $info = Info::find()->where(['id' => $id])->one();
            if($type == '1'){
                if($info->attach){
                    $info->attach .= ';'.$res['path'];
                }else{
                    $info->attach .= $res['path'];
                }
            }else{
                if($info->attach_copy){
                    $info->attach_copy .= ';'.$res['path'];
                }else{
                    $info->attach_copy .= $res['path'];
                }
            }
            $info->save();
            return Tools::json($res);
        }
    }

    /**
     * 删除新闻
     */
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $info = new info();
            $res = $info->delInfo($id);

            return $res;
        }
    }

    /**
     * 删除新闻图片
     */
    public function actionDelCov()
    {
        $request = \Yii::$app->request;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = $request->get('id');

        $info = Info::find()->where(['id' => $id])->one();
        $info->cover = '';

        if($info->save()){
            return ['code' => 1, 'data' => '', 'msg' => '删除封面成功'];
        }else{
            return ['code' => -1, 'data' => '', 'msg' => ''];
        }

    }

    /**
     * 删除新闻图片
     */
    public function actionDelAtt()
    {
        $request = \Yii::$app->request;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $str = explode(";", $request->get('str'));
        $att = $request->get('att');
        $info = Info::find()->where(['id' => $str['1']])->one();

        $pre_att = explode(";", ($str['0'])?$info->attach:$info->attach_copy);
        $new_att = null;
        foreach ($pre_att as $key){
            if($key != $att)
                $new_att .= $key.';';
        }
        //判断是否为唯一一个 如果$pre_att数组内只有一个，则为唯一一个
        if(isset($pre_att['1']))
            $new_att = substr($new_att, 0, -1);
        if($str['0']){
            $info->attach = $new_att;
        }else{
            $info->attach_copy = $new_att;
        }

        if($info->save()){
            return ['code' => 1, 'data' => '', 'msg' => '删除视频成功（请刷新）'];
        }else{
            return ['code' => -1, 'data' => '', 'msg' => ''];
        }

    }


    /**
     * 基础信息详情
     */
    public function actionDet()
    {
        $request = \Yii::$app->request;

        $show = $request->get('show');
        $info = Info::getInfoById($request->get('id'));

        //获取视频封面 和 文件
        if(strstr($info['title'], '视频')){
            $id = 1;
            $res = null;
            $res_copy = null;
            $arr = explode(';', $info['cover']);
            $arr_copy = explode(';', $info['cover_copy']);
            if($arr){
                foreach ($arr as $key){
                    $res[] = explode(':', $key);
                }
            }
            if($arr_copy){
                foreach ($arr_copy as $key){
                    $res_copy[] = explode(':', $key);
                }
            }
        }else{
            $id = 0;
            $res = $info['cover'];
            $res_copy = '';
        }
        return $this->render('det', [
            'id' => $id,
            'cover' => $res,
            'cover_copy' => $res_copy,
            'info' => $info,
            'show' => $show
        ]);
    }

    //批量删除
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $cinfo = new Info();
            $res = $cinfo->delCinfoSelected($ids);

            return $res;
        }
    }

    public function actionEdit()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post()['Info'];

            $Info = new Info();
            $res = $Info->editInfos($param);

            return $res;
        }


        //获取多文件地址
        $cinfo = Info::findOne($request->get('id'));
        if(strstr($cinfo->title, '视频')){
            $id = 1;
            $res = null;
            $res_copy = null;
            $arr = explode(';', $cinfo->cover);
            $arr_copy = explode(';', $cinfo->cover_copy);
            if($arr){
                foreach ($arr as $key){
                    $res[] = explode(':', $key);
                }
            }
            if($arr_copy){
                foreach ($arr_copy as $key){
                    $res_copy[] = explode(':', $key);
                }
            }
        }else{
            $id = 0;
            $res = $cinfo->cover;
            $res_copy = '';
        }
        //宣传视频 截取字符串
        $viewAtt = explode(";", $cinfo->attach);
        $teachAtt = explode(";", $cinfo->attach_copy);
        $Att = array_merge($viewAtt, $teachAtt);
        $dicts = Dictionary::getDictByPN('基础信息');
        return $this->render('edit', [
            'tid' => $id,
            'cover' => $res,
            'cover_copy' => $res_copy,
            'att' => $Att,
            'view' => $viewAtt,
            'teach' => $teachAtt,
            'cinfo' => $cinfo,
            'dicts' => $dicts,
            'id' => $request->get('id')
        ]);
    }

    public function actionAddPrice(){
        return $this->render('add-price');
    }

    public function actionPrc(){
        $request = \Yii::$app->request;

        if($request->isPost){
            $file = $_SERVER['DOCUMENT_ROOT'].'/upload/cover/price.png';
            $res = Tools::file_delete($file);
            //获取web根目录
            $docroot = "/upload/cover/";
            $res = Tools::file_upload_prc($docroot);
        }

        return Tools::json($res);
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id)
    {
        return [
            '编辑' => [
                'auth' => 'cinfo/edit',
                'href' => "javascript:cinfoEdit(&quot;" . $id . "&quot;)",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            '详情' => [
                'auth' => 'cinfo/det',
                'href' => "javascript:cinfoDet(&quot;" . $id . "&quot;)",
                'btnStyle' => 'info',
                'icon' => 'fa fa-paste'
            ],
            '删除' => [
                'auth' => 'cinfo/del',
                'href' => "javascript:cinfoDel(&quot;" . $id . "&quot;)",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ]
        ];
    }
}