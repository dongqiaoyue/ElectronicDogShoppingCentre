<?php

namespace common\models;
use api\helpers\HttpClient;

class Logistics
{
    //ali-deliver接口,
    static function express($com,$nu,$receiverPhone = 0,$senderPhone = 0)
    {
        $host = "https://ali-deliver.showapi.com";
        $path = "/showapi_expInfo";
        $method = "GET";
        $appcode = "b496b4ce3265442dad9fd587483c72b8";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);

        //$querys = "com=zhongtong&nu=535962308717&receiverPhone=receiverPhone&senderPhone=senderPhone";
        if($receiverPhone !=0 && $senderPhone != 0)
        {
            $querys = "com=".$com."&nu=".$nu."&receiverPhone=".$receiverPhone."&senderPhone=".$senderPhone."";
        }
        if($receiverPhone == 0 && $senderPhone == 0)
        {
            $querys = "com=".$com."&nu=".$nu."";
        }
        if($receiverPhone == 0 && $senderPhone !=0)
        {
            $querys = "com=".$com."&nu=".$nu."&senderPhone=".$senderPhone."";
        }
        if($receiverPhone != 0 && $senderPhone == 0)
        {
            $querys = "com=".$com."&nu=".$nu."&receiverPhone=".$receiverPhone."";
        }

        $bodys = "";
        $url = $host . $path . "?" . $querys;

        //$res = HttpClient::get($url,$querys);
        //return $res;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        //var_dump(curl_exec($curl));
        return curl_exec($curl);
    }
}
