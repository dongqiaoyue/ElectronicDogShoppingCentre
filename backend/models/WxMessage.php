<?php

namespace backend\models;

use common\helpers\Tools;
use yii\helpers\Json;
use Yii;
use api\helpers\HttpClient;

class WxMessage extends \yii\db\ActiveRecord
{
    /**
     * 发送订单模板消息
     */
    public function send_notice($openid,$trackID,$name){//name 快递公司名称
        //$conf = Yii::$app->params['weixin'];
        //$appid = $conf['appid'];
        //$app_secret = $conf['appSecret'];
//        $appid = 'wx79cb0a612897f300';
//        $app_secret = 'f8e40462235a56381867fae9b8248d6f';
//        //获取access_token
//        if (isset($_COOKIE['access_token'])){
//            //if (!empty($_COOKIE['access_token'])){
//            $access_token=$_COOKIE['access_token'];
//        }else{
//
//            //$json_token=$this->curl_post("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$app_secret.'");
//            $accessTokenUrl ="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$app_secret}";
//            $access_token = HttpClient::get($accessTokenUrl);
//            setcookie('access_token',$access_token,time()+7200);
//            //return $access_token;
//        }
        $access_token = Tools::getAccessToken();
        //模板消息
        $json_template = $this->json_template($openid,$trackID,$name);
        //解码
        //$accessToken = json_decode($access_token['token']);
        //$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$accessToken->access_token;
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token['token'];
        //$res=$this->curl_post($url,urldecode($json_template));
        $res = HttpClient::post($url,urldecode($json_template));
        return $res;
//        if ($res['errcode']==0){
//            return '发送成功';
//        }else{
//            return '发送失败';
//        }
    }
    /**
     * 将模板消息json格式化,订单发货提醒
     */
    public function json_template($openid,$trackID,$name){

        $template_id = 'Ww1K9NJXifJpLrInssj0a-EI23lxNBfr6XtaqwQJJSo';
        //模板消息
        $template=array(
            'touser'=>"$openid", //用户openid
            'template_id'=>"$template_id", //在公众号下配置的模板id
            //'url'=>".$uel.", //点击模板消息会跳转的链接
            'topcolor'=>"#7B68EE",
            'data'=>array(
                'first'=>array('value'=>urlencode("您的订单已发货"),'color'=>"#FF0000"),
                'delivername'=>array('value'=>urlencode("$name"),'color'=>'#FF0000'), //keyword需要与配置的模板消息对应
                'ordername'=>array('value'=>urlencode("$trackID"),'color'=>'#FF0000'),
                'remark' =>array('value'=>urlencode('备注：如有疑问请联系我们的客服'),'color'=>'#FF0000'),
                )
        );
        $json_template=json_encode($template);
        return $json_template;
    }

    /**
     * 发送投诉和有奖建议模板消息
     */
    public function complaint_notice($openid,$name,$title,$complaint){//$name 投诉人  $title 标题 $complaint 投诉内容
//        $appid = 'wx79cb0a612897f300';
//        $app_secret = 'f8e40462235a56381867fae9b8248d6f';
//        //获取access_token
//        if (isset($_COOKIE['access_token'])){
//            //if (!empty($_COOKIE['access_token'])){
//            $access_token=$_COOKIE['access_token'];
//        }else{
//
//            //$json_token=$this->curl_post("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$app_secret.'");
//            $accessTokenUrl ="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$app_secret}";
//            $access_token = HttpClient::get($accessTokenUrl);
//            setcookie('access_token',$access_token,time()+7200);
//            //return $access_token;
//        }
        $access_token = Tools::getAccessToken();
        //模板消息
        $json_template = $this->json_complaint_template($openid,$name,$title,$complaint);
        //解码
        //$accessToken = json_decode($access_token['token']);
        //return ['$access_token' => $access_token,'$accessToken' => $accessToken];
        //$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$accessToken->access_token;
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token['token'];
        $res = HttpClient::post($url,urldecode($json_template));
        return $res;
    }

    /**
     * 将模板消息json格式化,投诉受理提醒
     */
    public function json_complaint_template($openid,$name,$title,$complaint){

        $template_id = 'SphBDSnS722CHmPTGlg6IrNbiCy-35hubokxhCM3-A8';
        //模板消息
        $template=array(
            'touser'=>"$openid", //用户openid
            'template_id'=>"$template_id", //在公众号下配置的模板id
            //'url'=>".$uel.", //点击模板消息会跳转的链接
            'topcolor'=>"#7B68EE",
            'data'=>array(
                'first'=>array('value'=>urlencode("$title"),'color'=>"#FF0000"),
                'keyword1'=>array('value'=>urlencode("$name"),'color'=>'#FF0000'), //keyword需要与配置的模板消息对应
                'keyword2'=>array('value'=>urlencode("$complaint"),'color'=>'#FF0000'),
                'keyword3'=>array('value'=>urlencode(date("Y-m-d H:i:s")),'color'=>'#FF0000'),
            )
        );
        $json_template=json_encode($template);
        return $json_template;
    }
    /**
     * @param $url
     * @param array $data
     * @return mixed
     * curl请求
     */
    function curl_post($url , $data=array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}