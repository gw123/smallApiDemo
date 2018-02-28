<?php
namespace api\controller;

use api\model\User;
use api\service\Code;
use core\Controller;
use All;
use Qiniu\Auth;
use service\sys\SiteService;

/***
 * 小程序登录的逻辑
 * Class SiteController
 * @package api\controller
 */
class SiteController extends Controller{

    public  $speech_bucketName = 'speech';
    public  $image_bucketName = 'image';

    public function loginAction()
    {
        return $this->asJson(['test'=>'login']);
    }

    /**
     * @return string
     */
    public function  xcxLoginAction()
    {
        //验证字段  默认值  是否必填  过滤函数
        $rules = [
            ['code','',1]
        ];
        $data = $this->getField($rules,'get');

        $js_code = $data['code'];
        $appid = All::$app->params['wx_appid'];
        $secret = All::$app->params['wx_secret'];

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$js_code}&grant_type=authorization_code";
        $content = file_get_contents($url);
        $wx_data = json_decode($content,true);

        if(!isset($wx_data['openid'])) {
            return $this->fail(Code::NotAuth ,'权限验证失败');
        }
        $user = User::find()->where(['wx_openid'=>$wx_data['openid']])->one();
        if(empty($user)){
            $user = new User();
            $user->wx_openid = $wx_data['openid'];
            if(!$user->save()){
             return $this->fail(Code::SysError,'数据保存失败');
            }
        }

        $response_data = $user->getAttributes(['id','username','avatar','sex','is_init']);
        $userdata = $response_data;
        $userdata['wx_session_key'] = $wx_data['session_key'];
        $this->request->setSession('user',$userdata);

        $access_key = \All::$app->params['qiniu_accesskey'];
        $secret_key = \All::$app->params['qiniu_secretKey'];
        $auth = new Auth($access_key, $secret_key);
        $speech_token = $auth->uploadToken($this->speech_bucketName);
        $image_token = $auth->uploadToken($this->image_bucketName);
        $response_data['speech_upload_token'] = $speech_token;
        $response_data['image_upload_token'] = $image_token;

        $response_data['access_token'] = $this->request->getSessionId();

        return $this->success($response_data);
    }

 }