<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/3/10
 * Time: 下午5:25
 */
namespace Common\Library;
class HttpUtil
{
    public function https_request($url, $data = null,$header=null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt ($curl, CURLOPT_HEADER, 0);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if($header){
            curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}