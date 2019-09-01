<?php
namespace app\api\controller;

use app\api\model\User as UserModel;
use app\api\model\User;
use think\Session;

class Signin
{
    public function signIn(){
        $username = input('post.username');
        $password = input('post.password');

        if ($username == null || $password == null){
            return json(['errcode'=>2, 'msg'=>'username or passwd is null.']);
        }

        $db_user = new UserModel();
        $data_user = $db_user->where("username = '$username'")->find();

        $password = md5($password);
        $password = md5($password.$data_user->salt);

        if ($password == $data_user->passwd){
            $token = md5($data_user->passwd . $data_user->username . time());
            $db_user_token = new User();
            $db_user_token->save(['token'=>$token,'expire_time'=>time()+ 60*60*24],['username'=>$data_user->username]);

            return json(['errcode'=>0,'msg'=>'ok', 'data'=>['token'=>$token]]);
        }else{
            return json(['errcode'=>1, 'msg'=> 'passwd isn\'t correct.']);
        }
    }

}