<?php


namespace app\api\controller;


use app\api\model\Apply;
use app\api\model\Batch;
use app\api\model\Comment;
use think\App;

class Applymanage
{
    public function addApplication(){
        $fullname = input('post.fullname');
        $sex = input('post.sex');
        $stu_num = input('post.stu_num');
        $grade_class = input('post.grade_class');
        $phone = input('post.phone');
        $qq = input('post.qq');

        $db_apply_check = new Apply();
        $num = $db_apply_check->where("student_id = '$stu_num'")->count();
        if ($num > 0){
            return json(['errcode'=>1, 'msg'=>'exists']);
        }else{
            $db_apply = new Apply();
            $db_apply->fullname = $fullname;
            $db_apply->student_id = $stu_num;
            $db_apply->class = $grade_class;
            $db_apply->sex = $sex;
            $db_apply->phone = $phone;
            $db_apply->qq = $qq;
            $db_apply->save();

            return json(['errcode'=>0, 'msg'=>'ok']);
        }


    }
    public function getList(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $sort_by = input("sort_by");
        $sort_method = input("sort_method");

        $db_apply = new Apply();
        $data_apply = $db_apply->order($sort_by,$sort_method)->select();

        for($i=0;$i<count($data_apply);$i++){
            $db_comment = new Comment();
            $count_comment = $db_comment->where("aid = ".$data_apply[$i]->aid." AND username = '".getUsername()."'")->count();
            $data_apply[$i]['sum_rate'] = $db_comment->where("aid = ".$data_apply[$i]->aid)->avg("sum");
            if ($count_comment > 0){
                $data_apply[$i]['is_comment'] = 1;
            }else{
                $data_apply[$i]['is_comment'] = 0;
            }
        }

        return json(['errcode'=>0,'msg'=>'ok', 'data'=> $data_apply]);
    }

    public function firstCheck(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $first_status = input("post.status");
        $aid = input('post.aid');

        $db_apply = new Apply();
        $db_apply->save(['first_check'=>$first_status],['aid'=>$aid]);

        if($first_status == 1){


            $db_apply_check = new Apply();
            $check_data = $db_apply_check->where("aid",$aid)->find();

            $db_batch = new Batch();
            $batch_data = $db_batch->where("bid",$check_data->batch)->find();

            send_sms_first_check_ok($check_data->phone,$check_data->fullname,$batch_data->time,$batch_data->location);
        }




        return json(['errcode'=>0, 'msg'=>'ok']);



    }

    public function lastCheck(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $last_status = input("post.status");
        $aid = input('post.aid');

        $db_apply = new Apply();
        $db_apply->save(['last_check'=>$last_status],['aid'=>$aid]);

        if ($last_status == 1){
            $db_apply_check = new Apply();
            $check_data = $db_apply_check->where("aid",$aid)->find();

            send_sms_last_check_ok($check_data->phone,$check_data->fullname);
        }



        return json(['errcode'=>0, 'msg'=>'ok']);



    }

    public function otherCheck(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $last_status = input("post.status");
        $aid = input('post.aid');

        $db_apply = new Apply();
        $db_apply->save(['first_check'=>1,'last_check'=>$last_status],['aid'=>$aid]);

        if ($last_status == 1){
            $db_apply_check = new Apply();
            $check_data = $db_apply_check->where("aid",$aid)->find();

            send_sms_last_check_ok($check_data->phone,$check_data->fullname);
        }

        return json(['errcode'=>0, 'msg'=>'ok']);



    }

    public function doComment(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $username = getUsername();
        $aid = input("post.aid");
        $moralsRate = input("post.moralsRate");
        $characterRate = input("post.characterRate");
        $comprehensiveRate = input("post.comprehensiveRate");
        $skillRate =input("post.skillRate");
        $comment = input("post.comment");

        $db_comment = new Comment();
        $db_comment->aid = $aid;
        $db_comment->morals_rate = $moralsRate;
        $db_comment->comprehensive_rate = $comprehensiveRate;
        $db_comment->character_rate = $characterRate;
        $db_comment->skill_rate = $skillRate;
        $db_comment->sum = $moralsRate + $comprehensiveRate + $characterRate +  $skillRate;
        $db_comment->comment = $comment;
        $db_comment->username = $username;
        $db_comment->save();

        return json(['errcode'=>0, 'msg'=>'ok']);


    }

    public function getComment(){
        if (signCheck() == false){
            return json(['errcode'=>1, 'msg'=>'Authored failed']);
        }

        $aid = input('post.aid');

        $db_comment = new Comment();
        $data_comment = $db_comment->where("aid",$aid)->select();

        return json(['errcode'=>0, 'msg'=>'ok', 'data'=>$data_comment]);
    }

}