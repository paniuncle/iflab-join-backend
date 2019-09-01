<?php


namespace app\api\controller;


use app\api\model\User;

class UserManage
{
    public function getList(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $db_user = new User();
        $data_user = $db_user->select();
        for($i=0;$i<count($data_user);$i++){
            unset($data_user[$i]['passwd']);
            unset($data_user[$i]['salt']);
        }
        return json(['errcode'=>0, 'msg'=>'ok', 'data'=>$data_user]);
    }

    public function createUser(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $username = input('post.username');
        $password = input('post.password');
        $salt = rand(100000,999999);
        $password = md5($password);
        $password = md5($password.$salt);

        $db_user = new User();
        $db_user->username = $username;
        $db_user->passwd = $password;
        $db_user->salt = $salt;
        $db_user->save();

        return json(['errcode'=>0, 'msg'=>'ok']);


    }

    public function changePasswd(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }
        $new_password = input('post.password');
        $salt = rand(100000,999999);
        $new_password = md5($new_password);
        $new_password = md5($new_password.$salt);
        $username = input('post.username');

        $db_user = new User();
        $db_user->save(['passwd'=>$new_password,'salt'=>$salt],['username'=>$username]);

        return json(['errcode'=>0, 'msg'=>'ok']);

    }

    public function changeStatus(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }
        $username = input('post.username');

        $db_user = new User();
        $data_user = $db_user->where("username = '$username'")->find();
        if ($data_user->status == 0){
            $db_user->save(['status'=>1],['username'=>$username]);
        }else{
            $db_user->save(['status'=>0],['username'=>$username]);
        }

        return json(['errcode'=>0, 'msg'=>'ok']);
    }

}