<?php


namespace app\api\controller;


use app\api\model\Apply;
use app\api\model\Batch;
use think\App;

class Batchmanage
{
    public function getList(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $db_batch = new Batch();
        $data_batch = $db_batch->where("status = 1")->select();
        for($i=0;$i<count($data_batch);$i++){
            $data_batch[$i]['time'] = date('Y-m-d H:i',strtotime($data_batch[$i]['time']));
        }
        return json(['errcode'=>0,'msg'=>'ok','data'=>$data_batch]);

    }

    public function addBatch(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $name = input("post.name");
        $time = input("post.time");
        $location = input("post.location");
        $db_batch = new Batch();
        $db_batch->name = $name;
        $db_batch->time = $time;
        $db_batch->location = $location;
        $db_batch->save();
        return json(['errcode'=>0,'msg'=>'ok']);


    }

    public function delBatch(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $bid = input("post.bid");
        $db_batch = new Batch();
        $db_batch->save(['status'=>0],['bid'=>$bid]);
        return json(['errcode'=>0,'msg'=>'ok']);
    }

    public function setUserBatch(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $input_data = input("post.user");
        $input_data = json_decode($input_data);
        $batch = input("post.batch");

        $end_data = [];

        for ($i=0;$i<count($input_data);$i++){
            $end_data[$i] = ['aid'=>$input_data[$i],'batch'=>$batch];
        }

        $db_apply = new Apply();
        $db_apply->saveAll($end_data);
        return json(['errcode'=>0,'msg'=>'ok']);
    }

}