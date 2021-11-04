<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/7/18
 * Time: 13:48
 */
namespace backend\controllers;

use backend\controllers\BaseController;
use backend\models\Users;
use common\helpers\Tools;
use common\models\User;
use yii\web\Response;
use yii;

class UinfoController extends BaseController
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            if (!empty($param['searchText'])) {
                $where = ['like', 'phone', $param['searchText']];
            }

            $selectResult = Users::getUsersByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){
                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['id']));
            }

            $return['total'] = Users::getUsersNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }


        return $this->render('index');
    }

    //用户详情
    public function actionDet()
    {
        $request = \Yii::$app->request;

        $info = Users::getUserById($request->get('id'));
        //添加时间
        $info['addAt']=date("Y-m-d H:i:s",$info['addAt']);
        return $this->render('det', [
            'info' => $info
        ]);
    }

    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $user = new Users();
            $res = $user->delUser($id);

            return $res;
        }
    }

    public function actionAdd()
    {

        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $param['password'] = Yii::$app->getSecurity()->generatePasswordHash($param['password']);

            $user = new Users();
            $res = $user->addUser($param);

            return $res;
        }

        return $this->render('add');
    }

    //批量删除
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $uinfo = new Users();
            $res = $uinfo->delUinfoSelected($ids);

            return $res;
        }
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id)
    {
        return [
            '详情' => [
                'auth' => 'uinfo/det',
                'href' => "javascript:uinfoDet(&quot;" . $id . "&quot;)",
                'btnStyle' => 'info',
                'icon' => 'fa fa-paste'
            ],
            '删除' => [
                'auth' => 'uinfo/del',
                'href' => "javascript:uinfoDel(&quot;" . $id . "&quot;)",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ]
        ];
    }

}
