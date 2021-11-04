<?php
namespace common\helpers;
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:17
 */

use api\helpers\HttpClient;
use common\models\Accesstoken;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\Url;

class Tools
{
    /**
     * 生成操作按钮
     * @param array $operate
     * @return string
     */
    public static function showOperate($operate = [])
    {
        if(empty($operate)){
            return '';
        }

        $option = '';
        foreach($operate as $key => $vo){
            if(self::authCheck($vo['auth']) || (1 == \Yii::$app->session->get('admin_id'))){
                $option .= ' <a href="' . $vo['href'] . '"><button type="button" class="btn btn-' . $vo['btnStyle'] . ' btn-sm">'.
                    '<i class="' . $vo['icon'] . '"></i> ' . $key . '</button></a>';
            }
        }

        return $option;
    }

    /**
     * 权限检测
     * @param $rule
     * @return bool
     */
    public static function authCheck($rule)
    {
        $cache = \Yii::$app->cache;
        $session = \Yii::$app->session;

        // 超级管理员跳过权限
        if(1 == $session->get('admin_id')){
            return true;
        }

        $key = 'role_' . $session->get('role_id') . '_auth';
        $authRule = empty($cache->get($key)) ? [] : unserialize($cache->get($key));

        // 可跳过的权限节点
        $skipRule = [
            'site/index',
            'site/first'
        ];

        if(in_array($rule, array_merge($authRule, $skipRule))){
            return true;
        }

        return false;
    }

    /**
     * json压缩
     * @param $data
     * @return string
     */
    public static function json($data)
    {
        return json_encode($data);
    }

    /**
     * 根据就是生成菜单
     * @param $roleId
     * @return array
     */
    public static function makeMenu($roleId)
    {
        if(1 == $roleId){

            // 超级管理员是整个菜单
            $menu = (new Query())->from('rbac_nodes')->where(['is_menu' => 2])->all();
        }else {

            $menu = unserialize(\Yii::$app->cache->get('role_' . $roleId . '_menu'));
        }

        return self::getTree($menu);
    }

    /**
     * 整理出tree数据
     * @param $pInfo
     * @param $spread
     */
    public static function getTree($pInfo)
    {
        $res = [];
        $tree = [];

        // 整理数组
        foreach($pInfo as $key => $vo){
            $res[$vo['ID']] = $vo; //$vo 键名
            $res[$vo['ID']]['children'] = [];
        } //配置顶级菜单
        unset($pInfo);

        // 查找子孙
        foreach($res as $key => $vo){
            if(0 != $vo['parentID']){
                $res[$vo['parentID']]['children'][] = &$res[$key];
            }
        }

        // 过滤杂质
        foreach( $res as $key => $vo ){
            if(0 == $vo['parentID']){
                $tree[] = $vo;
            }
        }
        unset( $res );

        return $tree;
    }

    /**
     * 生成32位guid唯一标识
     * 所有id都用这个生成
     * @return string
     */
    public static function create_id()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);
        $uuid =
            substr($charid, 0, 8)
            .substr($charid, 8, 4)
            .substr($charid,12, 4)
            .substr($charid,16, 4)
            .substr($charid,20,12)
        ;
        return $uuid;
    }

    /**
     * 生成8位运单号
     * 所有id都用这个生成
     * @return string
     */
    public static function create_trackID()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);
        $uuid =
            substr($charid, 0, 8)
//            .substr($charid, 8, 4)
//            .substr($charid,12, 4)
//            .substr($charid,16, 4)
//            .substr($charid,20,12)
        ;
        return $uuid;
    }

    /**
     * 获取客户端IP
     * @return string 返回ip地址,如127.0.0.1
     */
    public static function getClientIp()
    {
        $onlineip = 'Unknown';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ips = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $real_ip = $ips['0'];
            if ($_SERVER['HTTP_X_FORWARDED_FOR'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $real_ip))
            {
                $onlineip = $real_ip;
            }
            elseif ($_SERVER['HTTP_CLIENT_IP'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP']))
            {
                $onlineip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        if ($onlineip == 'Unknown' && isset($_SERVER['HTTP_CDN_SRC_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CDN_SRC_IP']))
        {
            $onlineip = $_SERVER['HTTP_CDN_SRC_IP'];
            $c_agentip = 0;
        }
        if ($onlineip == 'Unknown' && isset($_SERVER['HTTP_NS_IP']) && preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['HTTP_NS_IP'] ))
        {
            $onlineip = $_SERVER ['HTTP_NS_IP'];
            $c_agentip = 0;
        }
        if ($onlineip == 'Unknown' && isset($_SERVER['REMOTE_ADDR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['REMOTE_ADDR']))
        {
            $onlineip = $_SERVER['REMOTE_ADDR'];
            $c_agentip = 0;
        }
        return $onlineip;
//        return $_SERVER['HTTP_X_REAL_IP'];
    }

    /**
     * 获得访问者浏览器
     */
    public static function browse_info() {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i', $br)) {
                $br = 'MSIE';
            } else if (preg_match('/Firefox/i', $br)) {
                $br = 'Firefox';
            } else if (preg_match('/Chrome/i', $br)) {
                $br = 'Chrome';
            } else if (preg_match('/Safari/i', $br)) {
                $br = 'Safari';
            } else if (preg_match('/Opera/i', $br)) {
                $br = 'Opera';
            } else {
                $br = 'Other';
            }
            return $br;
        } else {
            return 'unknow';
        }
    }

    /**
     * 文件删除
     */
    public static function file_delete($cover)
    {
        if(file_exists($cover)) {
            if (unlink($cover)) {
                return ['code' => 1, 'msg' => '删除文件成功'];
            } else {
                return ['code' => -1, 'msg' => '删除文件错误'];
            }
        } else {
            return ['code' => -1, 'msg' => '文件不存在'];
        }
    }

    /**
     * 删除多个文件
     */
    public static function file_delete_more($covers)
    {
        $res = ['code' => 1, 'msg' => '删除文件成功'];

        foreach ($covers as $cover) {
            $file = $_SERVER['DOCUMENT_ROOT'].$cover->url;
            if(file_exists($file)){
                if(unlink($file)){
                    continue;
//                    return ['code' => 1, 'msg' => '删除文件成功'];
                }else{
                    $res = ['code' => -1, 'msg' => '删除文件错误'];
                    break;
//                    return ['code' => -1, 'msg' => '删除文件错误'];
                }
            }else{
                $res = ['code' => -1, 'msg' => '文件不存在'];
                break;
//                return ['code' => -1, 'msg' => '文件不存在'];
            }
        }

        return $res;
    }

    /**
     * 单文件上传
     */
    public static function file_upload($docroot)
    {

        //生成文件存储路径
        $fileupload = $_SERVER['DOCUMENT_ROOT'].$docroot.$_FILES['file']['name'];

        //本地保存文件
        if (file_exists($fileupload)) {
            return  ['code' => 0, 'data' => '', 'msg' => '文件已经存在'];
        } else {
            $extpos = strrpos($_FILES['file']['name'],'.'); //查找指定字符串最后一次出现的位置
            $ext = substr($_FILES['file']['name'],$extpos+1); //从固定位置截取字符串
            $filename = time().'.'.$ext;
            $path = $_SERVER['DOCUMENT_ROOT'].$docroot.$filename;
            // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
            move_uploaded_file($_FILES['file']['tmp_name'], $path);
            sleep(1);
            return  ['code' => 1, 'data' => '', 'msg' => '上传文件成功', 'path' => $docroot.$filename];
        }
    }

    public static function file_upload_prc($docroot)
    {

        //生成文件存储路径
        $fileupload = $_SERVER['DOCUMENT_ROOT'].$docroot.$_FILES['file']['name'];

        //本地保存文件
        if (file_exists($fileupload)) {
            return  ['code' => 0, 'data' => '', 'msg' => '文件已经存在'];
        } else {
            $extpos = strrpos($_FILES['file']['name'],'.'); //查找指定字符串最后一次出现的位置
            $ext = substr($_FILES['file']['name'],$extpos+1); //从固定位置截取字符串
            $filename = 'price.'.'png';
            $path = $_SERVER['DOCUMENT_ROOT'].$docroot.$filename;
            // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
            move_uploaded_file($_FILES['file']['tmp_name'], $path);
            sleep(1);
            return  ['code' => 1, 'data' => '', 'msg' => '上传文件成功', 'path' => $docroot.$filename];
        }
    }

    /**
     * @param $docroot
     * 多文件上传
     */
    public static function file_upload_more($files)
    {
//        $files = $files['file'];
        //判断文件是否可以上传到服务器 $_FILES['myfile'][error]为0表示成功
        for( $i = 0;$i < count($files); $i++ ){
//            $key = array_keys($files['error'])[$i]; //取出当前图片对应的键名
            if($files[$i]['error']>0){

                switch($files[$i]['error']){

                    case 1: return  ['code' => 0, 'data' => '', 'msg' => '第'.($i+1).'个文件大小超出约定值'];
                    case 2: return  ['code' => 0, 'data' => '', 'msg' => '上传第'.($i+1).'个文件大小超出了约定值'];
                    case 3: return  ['code' => 0, 'data' => '', 'msg' => '第'.($i+1).'个文件只被部分上传'];
                    case 4: return  ['code' => 0, 'data' => '', 'msg' => '第'.($i+1).'个文件没有上传'];
                    default: return  ['code' => 0, 'data' => '', 'msg' => '未知错误'];
                }
            }


            /*也可通过获取上传文件的MIME类型中的主类型和子类型，来限制文件上传的类型
            list($maintype,$subtype) = explode("/",$_FILES['myfile']['type']);
            if($maintype == "text"){

                die("不能上传文本文件");
            }
            */

            //判断上传的文件是否允许大小
            $size = '50000000';

            if($files[$i]['size']>$size){
                return  ['code' => 0, 'data' => '', 'msg' => "第".($i+1)."个文件超过了允许的<b>{$size}</b>"];
            }
            //为了系统安全，同时也为了同名文件不被覆盖，上传后将文件名使用系统定义
            $extpos = strrpos($files[$i]['name'],'.'); //查找指定字符串最后一次出现的位置
            $ext = substr($files[$i]['name'],$extpos+1); //从固定位置截取字符串
            $filename = time().'.'.$ext;
            if($ext == 'avi' || $ext == 'mp4'){
                $path = $_SERVER['DOCUMENT_ROOT']."/upload/video/".$filename;
            }else{
                $path = $_SERVER['DOCUMENT_ROOT']."/upload/cover/".$filename;
            }

            //判断是否为上传文件
            if(is_uploaded_file($files[$i]['tmp_name'])){

                if(!move_uploaded_file($files[$i]['tmp_name'], $path)){
                    return  ['code' => 0, 'data' => '', 'msg' => '不能将文件移动到指定位置 '];
                }else{
                    //如果成功
                    sleep(1);
                    if($ext == 'avi' || $ext == 'mp4'){
                        $url[$i]['url'] = "/upload/video/".$filename;
                    }else{
                        $url[$i]['url'] = "/upload/cover/".$filename;
                    }
                }
            }else{
                return  ['code' => 0, 'data' => '', 'msg' => '上传的不是合法文件'];
            }


        }
        //如果文件上传成功
        return  ['code' => 1, 'msg' => '文件上传成功', 'path' => $url];

    }

    public static function buildUrl( $path,$params = [] ){
        $domain_config = \Yii::$app->params['domain'];
        $path = Url::toRoute(array_merge([$path], $params));

        return $domain_config['online'].$path;
    }

    public static function buildWwwUrl( $path,$params = [] ){
        $domain_config = \Yii::$app->params['domain'];
        $path = Url::toRoute(array_merge([ $path ],$params));
        return $domain_config['online'].$path;
    }

    public static function buildOauthUrl( $path,$params = [] ){
        $domain_config = \Yii::$app->params['domain'];
//        $path = Url::toRoute(array_merge([$path], $params));
        $res = $domain_config['online'].$path.'?t='.$params['t'];
        return $res;
    }

    public static function findNum($str=''){
        $str=trim($str);
        if(empty($str)) {
            return '';
        }
        $temp=array('1','2','3','4','5','6','7','8','9','0');
        $numRes='';
        $disRes='';

        //截取成两段
        list($num, $dis) = explode('单价为', $str);
        for($i=0;$i<strlen($num);$i++){
            if(in_array($num[$i],$temp)){
                $numRes.=$num[$i];
            }
        }
        for($i=0;$i<strlen($dis);$i++){
            if(in_array($dis[$i],$temp)){
                $disRes.=$dis[$i];
            }
        }

        return ['num' => $numRes, 'dis' => $disRes];
    }
    public static function trimall($str)//删除空格
    {
        $oldchar=array(" ","　","\t","\n","\r");
        $newchar=array("","","","","");
        return str_replace($oldchar,$newchar,$str);
}

    public static function getAccessToken(){
//        //获取用户open_id
//        $cookie = \Yii::$app->request->cookies;
//        $auth_cookie = $cookie->get("shop_member");
//        if ($auth_cookie)
//            list($auth_token, $open_id, $user_id) = explode("#", $auth_cookie);


        $res = Accesstoken::find()->orderBy(['expAt' => SORT_DESC])->one();
        if(isset($res) && $res->expAt > time())
            return ['token' => $res->access_token];

        $conf = \Yii::$app->params['weixin'];
        //通过code来向服务器请求access_token
        $accessTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$conf['appid']}&secret={$conf['appSecret']}";
        $accesstoken = HttpClient::get($accessTokenUrl);
        $data = Json::decode($accesstoken);
        $token = isset($data['access_token']) ? $data['access_token'] : '';

        if(!isset($data['errcode']) && $token != ''){
            $access_token = new Accesstoken();
            $access_token->id = self::create_id();
            $access_token->access_token = $token;
            $access_token->expAt = time() + 7200;
            $access_token->addAt = time();
            $access_token->addBy = '';
            $access_token->addAgent = self::browse_info();
            $access_token->addIP = self::getClientIp();
            if($access_token->save()){
                return ['token' => $token];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}