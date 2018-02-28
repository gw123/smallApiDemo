<?php
namespace api\controller;

use All;
use api\model\Article;
use api\model\User;
use api\service\Code;
use core\Controller;

 class UserController extends Controller{

     public function setUserInfoAction()
     {
         $user_session = $this->request->getSession('user');
         $user_session = json_decode($user_session,true);

         $rules = [
             ['rawData,signature,encryptedData,iv','',1],
             ['userInfo','',1,'json_decode']
         ];
         $request_data = $this->getField($rules,'post');
         if($request_data===false){
             return $this->fail(Code::ArgumentError,$this->fields_error);
         }
         $cmp_signature = sha1($request_data['rawData'].$user_session['wx_session_key']);
         if($cmp_signature!=$request_data['signature']) {
            $this->fail(Code::NotAuth ,'权限验证失败');
         }

         $userData = json_decode($request_data['rawData'],true);
         $user = User::findOne(['id'=>$user_session['id']]);

         if($user->is_init==0){
             $user->username = $userData['nickName'];
             $user->sex = $userData['gender'];
             $user->avatar = $userData['avatarUrl'];
             $user->is_init = 1;
             if($user->save())
             {
                 return $this->success([]);
             }else{
                 return $this->fail(Code::SysError,'保存失败');
             }
         }else{
             return $this->success([]);
         }
     }

     public function getUserNotesAction()
     {
         if(!$this->user){
             return $this->fail(Code::NotAuth,'请登录后使用');
         }

         $id = $this->user['id'];
         $rows = Article::find()->select('id,title,created_time')->where(['uid'=>$id])->asArray()->limit(20)->orderBy('id desc')->all();

         return  $this->success(['list'=>$rows,'tip'=>'当前系统只支持保存20篇笔记']);
     }

     public function getNoteDetailAction()
     {
         if(!$this->user){
             return $this->fail(Code::NotAuth,'请登录后使用');
         }

         $id = isset($this->request->_get['id'])? intval($this->request->_get['id']) : null;
         if(empty($id)){
             return $this->fail(Code::ArgumentError , '缺少必须的参数');
         }

         $article = Article::findOne(['id'=>$id]);
         if(!$article){
             return $this->fail(Code::NotFound , '笔记不存在');
         }

         $article = $article->getAttributes();

         return $this->success(['note'=>$article]);

     }
 }