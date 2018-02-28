<?php
namespace api\controller;

use api\model\Article;
use api\service\baidu\AipOcr;
use api\service\Code;
use core\Controller;
use Qiniu\Auth;

/***
 * 百度图片识别，语言识别
 * Class ToolController
 * @package api\controller
 */
class ToolController extends Controller{
    public  $bucketName = 'speech';
    public $enableCsrfValidation = false;
    public function  getTextAction()
    {
        if(!$this->user){
            return $this->fail(Code::NotAuth,'请登录后使用');
        }
        $data_host = \All::$app->params['qiniu_download_host2'];
        if(!isset($this->request->_get['key'])) {
            return $this->fail(Code::ArgumentError ,'参数校验失败');
        }
        $key = $this->request->_get['key'];
        $speech_url = $data_host.'/'.$key;

        $imageRaw = file_get_contents($speech_url);
        if(empty($imageRaw)){
            return $this->fail(Code::ArgumentError ,'不存在的文件');
        }

        $appId = \All::$app->dynamic_params['bd_ocr_appid'];
        $appKey = \All::$app->dynamic_params['bd_ocr_appkey'];
        $secretKey = \All::$app->dynamic_params['bd_ocr_secretkey'];

        $client = new AipOcr($appId,$appKey,$secretKey);
        $result = $client->basicGeneral($imageRaw);

        if(!isset($result['words_result'])){
            return $this->fail(Code::SysError,'解析失败');
        }
        $rows = [];
        foreach ($result['words_result'] as $row){
            $rows[] = $row['words'];
        }
        $rows_str = implode("\n",$rows);
        return $this->success(['text'=>$rows_str]);
    }

    /**
     * @return boolean
     */
    public function saveResultAction()
    {
        if(!$this->user){
            return $this->fail(Code::NotAuth,'请登录后使用');
        }
        $rules = [
            ['id',''],
            ['title','',1],
            ['text','',1]
        ];
        $request_data = $this->getField($rules ,'post');
        if($request_data===false){
            return $this->fail(Code::ArgumentError,$this->fields_error);
        }
        if(isset($request_data['id'])){
            $article = Article::findOne(['id'=>$request_data['id']]);
        }
        if(!isset($article)||!$article){
            $article = new  Article();
        }

        $data['title'] =  $request_data['title'];
        $data['content'] = $request_data['text'];

        $article->load($data,'');

        $article->uid =  $this->user['id'];
        if($article->save()){
            return $this->success(['id'=>$article->id]);
        }
    }

    public function  getUploadTokenAction()
    {
        if(!$this->user){
            return $this->fail(Code::NotAuth,'请登录后使用');
        }
        $access_key = \All::$app->params['qiniu_accesskey'];
        $secret_key = \All::$app->params['qiniu_secretKey'];
        $auth = new Auth($access_key, $secret_key);
        $token = $auth->uploadToken($this->bucketName);
        return $this->success(['token'=>$token]);
    }

    public function  speechToTextAction(){
        if(!$this->user){
            return $this->fail(Code::NotAuth,'请登录后使用');
        }

        $data_host = \All::$app->params['qiniu_download_host'];
        if(!isset($this->request->_get['key'])) {
            return $this->fail(Code::ArgumentError ,'参数校验失败');
        }
        $key = $this->request->_get['key'];
        $speech_url = $data_host.'/'.$key;
        $content = file_get_contents($speech_url);
        if(empty($content)){
            return $this->fail(Code::ArgumentError ,'不存在的文件');
        }

        $temp_name = $this->makeTmpSpeechName();
        file_put_contents($temp_name,$content);
        $cmd = "ffmpeg -i {$temp_name} -acodec pcm_s16le -ar 8000  -ac 1 {$temp_name}.wav >> /dev/null";
        exec($cmd);
        unlink($temp_name);
        $appId = \All::$app->dynamic_params['bd_speech_appid'];
        $appKey = \All::$app->dynamic_params['bd_speech_appkey'];
        $secretKey = \All::$app->dynamic_params['bd_spech_secretkey'];

        $baiduAPath =  APP_PATH.'/service/speech/AipSpeech.php';
        require_once($baiduAPath);

        $client = new \AipSpeech($appId,$appKey,$secretKey);
        $content = file_get_contents($temp_name.".wav");
        unlink($temp_name.'.wav');
        $result = $client->asr($content,'wav',8000 ,array(
            'lan' => 'zh',
        ));

        if(isset($result['err_no'])&&$result['err_no']===0)
        {
            return $this->success(['text'=>$result['result'][0]]);
        }
        return $this->fail(Code::TooBad,'音质太差,请重新录入');
    }

    function  makeTmpSpeechName()
    {
        $session_id = $this->request->getSessionId();
        $filename = md5($session_id.time().rand(1000,9999)).".mp3";
        $path = APP_PATH."/runtime/speech";
        if(!is_dir($path)){
            mkdir($path,0666,true);
        }
        return $path.'/'.$filename;
    }
}