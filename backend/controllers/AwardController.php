<?php
namespace backend\controllers;
use backend\models\Advice;
use backend\models\Dictionary;
use common\helpers\Tools;
use yii\web\Response;

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/9/13
 * Time: 12:17
 */
class AwardController extends BaseController
{
    public function actionIndex(){
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            if (!empty($param['searchText'])) {
                $where = ['like', $param['searchType'], $param['searchText'],];
            } elseif($param['checkStatus']!="#") {
                $where = ['like', 'status', $param['checkStatus']];
            } else {
                $where = ['like', $param['searchType'], $param['searchText'],];
            }

            $selectResult = Advice::getAdviceByWhere($where, $offset, $limit);

            $status = Advice::getStatus();


            // 拼装参数
            foreach($selectResult as $key => $vo){

                // 状态
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
                // 姓名
                isset($title[$vo['name']]) && $selectResult[$key]['name'] = $title[$vo['name']];
                // 时间
                $selectResult[$key]['addAt']=date("Y-m-d H:i:s", $selectResult[$key]['addAt']);


                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['id'],$vo['status']));

            }

            $return['total'] = Advice::getAdviceNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }


    //投诉详情
    public function actionDet()
    {
        $request = \Yii::$app->request;

        $info = Advice::getAdviceById($request->get('id'));

        //地区信息
        $region = Advice::getAdviceRegionById($request->get('id'));
        //详细地址
        $addr = $info['addr'];
        //拼接
        $info['address'] = $region.$addr;
        //状态
        $status = Advice::getStatus();
        $info['status'] =  $status[$info['status']];

        return $this->render('det', [
            'info' => $info
        ]);
    }

    // 删除投诉信息
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $advice = new Advice();
            $res = $advice->delAdvice($id);

            return $res;
        }
    }

    //批量删除
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $advice = new Advice();
            $res = $advice->delAdviceSelected($ids);

            return $res;
        }
    }

    // 处理投诉信息
    public function actionDeal()
    {
        $request = \Yii::$app->request;
        //var_dump( $request->post('result'));
        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;

            $param =$request->post();
            $result = $request->post('result');

            $id = $request->post('id');
            $res = Advice::dealAdvice($param['id'], $param['result']);

            return $res;
        }

        $id = $request->get('id');
        return $this->render('result',[
            'id' => $id
        ]);
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id, $status)
    {
        if($status==0)
        {
            return [
                '详情' => [
                    'auth' => 'award/det',
                    'href' => "javascript:awardDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'award/del',
                    'href' => "javascript:awardDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ],
                '处理' => [
                    'auth' => 'award/deal',
                    'href' => "javascript:awardDeal('$id')",
                    'btnStyle' => 'success',
                    'icon' => 'fa fa-check-square-o'
                ]
            ];
        } else {
            return [
                '详情' => [
                    'auth' => 'award/det',
                    'href' => "javascript:awardDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'award/del',
                    'href' => "javascript:awardDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ],
            ];
        }
    }
}

