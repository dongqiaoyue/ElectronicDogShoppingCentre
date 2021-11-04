<?php
namespace common\models;



class Santos
{
    static function extract_msgid($resp)
    {
        preg_match('/mtmsgid=(.*?)&/', $resp, $re);
        if (!empty($re) && count($re) >= 2)
            return $re[1];

        return "";
    }

    /*
     * @cpid string Api 帐号
     * @cppwd string Api 密码
     * @to  number  目的地号码，国家代码+手机号码（国家号码、手机号码均不能带开头的0）
     * @content string 短信内容
     *
     * @Return string 消息ID，如果消息ID为空，或者代码抛出异常，则是发送未成功。
     * */
    static function send($cpid, $cppwd, $to, $content)
    {
        $c = urlencode($content);

        // http接口，支持 https 访问，如有安全方面需求，可以访问 https开头
        $api = "http://api2.santo.cc/submit?command=MT_REQUEST&cpid={$cpid}&cppwd={$cppwd}&da={$to}&sm={$c}";

        // 建议记录 $resp 到日志文件，$resp里有详细的出错信息
        try {
            $resp = file_get_contents($api);
        } catch(\Exception $e){
            echo $e->getMessage();

            return "";
        }

        return Santos::extract_msgid($resp);
    }

}

if (!count(debug_backtrace()))
{
    $smsID = Santos::send("test1", "", "886905076559", "您的验证码短信是135790，5分钟内有效。");
    echo $smsID;
}