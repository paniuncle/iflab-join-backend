<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Request;
use think\Session;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

system_init();

function system_init(){
    header("Access-Control-Allow-Origin: http://localhost:8080");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Content-Type,Cookie,Authorization');


    if (Request::instance()->method() == "OPTIONS"){

        header("HTTP/1.1 204 No Content");
        die;
    }


}

function signCheck(){
    $user_token = Request::instance()->header('Authorization');

    $db_user = new \app\api\model\User();
    $data_token = $db_user->where("token = '$user_token'")->find();




    if ($data_token != null){
        if ($data_token->expire_time < time()){
            return false;
        }else{
            return true;
        }
    }else{
        return false;
    }
}

function getUsername(){
    $user_token = Request::instance()->header('Authorization');
    $db_user = new \app\api\model\User();
    $data_token = $db_user->where("token = '$user_token'")->find();
    return $data_token->username;
}
function get_accessKeyID(){
    return "<accessKeyID>";
}
function get_accessKeySecret(){
    return "<accessKeySecret>";
}

function send_sms_first_check_ok($phone,$name,$date,$location){

    $date = date('Y年m月d日 H:i',strtotime($date));
    AlibabaCloud::accessKeyClient(get_accessKeyID(), get_accessKeySecret())
        ->regionId('cn-hangzhou') // replace regionId as you need
        ->asDefaultClient();

    try {
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            // ->scheme('https') // https | http
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "default",
                    'PhoneNumbers' => $phone,
                    'SignName' => "ifLab",
                    'TemplateCode' => "SMS_173251785",
                    'TemplateParam' => "{'name':'$name','date':'$date','location':'$location'}",
                ],
            ])
            ->request();
        return true;
    } catch (ClientException $e) {
        return false;
    } catch (ServerException $e) {
        return false;
    }
}

function send_sms_last_check_ok($phone,$name){
    AlibabaCloud::accessKeyClient(get_accessKeyID(), get_accessKeySecret())
        ->regionId('cn-hangzhou') // replace regionId as you need
        ->asDefaultClient();

    try {
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            // ->scheme('https') // https | http
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "default",
                    'PhoneNumbers' => $phone,
                    'SignName' => "ifLab",
                    'TemplateCode' => "SMS_173340337",
                    'TemplateParam' => "{'name':'$name'}",
                ],
            ])
            ->request();
        return true;
    } catch (ClientException $e) {
        return false;
    } catch (ServerException $e) {
        return false;
    }
}