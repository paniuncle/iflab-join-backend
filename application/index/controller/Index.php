<?php
namespace app\index\controller;

use think\Config;

class Index
{
    public function index()
    {
        //Config::set("test","test");
        //echo Config::get("test");

        $password = "12345";

        $password = md5($password);
        $password = md5($password.'123456');
        echo $password;
    }
}
