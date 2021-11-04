<?php
namespace backend\controllers;

use backend\models\Complaint;
use backend\models\Dictionary;
use common\helpers\Tools;
use yii\db\Query;
use yii\web\Response;

class ComplaintController extends BaseController
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
                $where = ['like', $param['searchType'], $param['searchText'],];
            } elseif($param['checkStatus']!="#") {
                $where = ['like', 'status', $param['checkStatus']];
            } else {
                $where = ['like', $param['searchType'], $param['searchText'],];
            }

            $selectResult = Complaint::getComplaintByWhere($where, $offset, $limit);

            $status = Complaint::getStatus();
            $title = Complaint::getTitle();


            // 拼装参数
            foreach($selectResult as $key => $vo){

                // 状态
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
                //原因
                isset($title[$vo['title']]) && $selectResult[$key]['title'] = $title[$vo['title']];
                //时间
                $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);


                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['id'],$vo['status']));

                //期望
                $dictionary = Dictionary::find()->where(['Code' => $selectResult[$key]['expectation']])->one();
                $selectResult[$key]['expectation'] = $dictionary['Name'];

            }

            $return['total'] = Complaint::getComplaintNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    //投诉详情
    public function actionDet()
    {
        $request = \Yii::$app->request;

        $info = Complaint::getComplaintById($request->get('id'));

        //状态
        $status = Complaint::getStatus();
        $info['status'] =  $status[$info['status']];

        //期望
        $dictionary = Dictionary::find()->where(['Code' => $info['expectation']])->one();
        $info['expectation'] = $dictionary['Name'];

        //原因
        $title = Complaint::getTitle();
        $info['title'] = $title[$info['title']];

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

            $admin = new Complaint();
            $res = $admin->delComplaint($id);

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

            $agent = new Complaint();
            $res = $agent->delComplaintSelected($ids);

            return $res;
        }
    }

    // 处理投诉信息
    public function actionDeal()
    {
        $request = \Yii::$app->request;
        //var_dump( $request->post('result'));
        if($request->isAjax){
            //return '1';
            \Yii::$app->response->format = Response::FORMAT_JSON;
            //return '1';
            //$id = $request->get('id');

//            $res = Complaint::dealComplaint($id);
//            return $res;
            $param =$request->post();
            $result = $request->post('result');

            $id = $request->post('id');
            $res = Complaint::dealComplaint($param['id'],$param['result']);
            //$res = Complaint::dealComplaint($id,$result);
            return $res;
            //return $param;

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
    private function makeButton($id,$status)
    {
        if($status==0)
        {
            return [
                '详情' => [
                    'auth' => 'complaint/det',
                    'href' => "javascript:complaintDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'complaint/del',
                    'href' => "javascript:complaintDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ],
                '处理' => [
                    'auth' => 'complaint/deal',
                    'href' => "javascript:complaintDeal('$id')",
                    'btnStyle' => 'success',
                    'icon' => 'fa fa-check-square-o'
                ]
            ];
        } else {
            return [
                '详情' => [
                    'auth' => 'complaint/det',
                    'href' => "javascript:complaintDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'complaint/del',
                    'href' => "javascript:complaintDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ],
            ];
        }
    }

}