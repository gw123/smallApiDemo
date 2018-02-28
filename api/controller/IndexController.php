<?php
namespace api\controller;
use \All;
use api\task\CountTask;
use api\task\MailTask;
use core\Controller;


class  IndexController extends Controller
{
    /***
     * 判断请求方式
     * @return string
     */
    public function indexAction()
    {
        $msg = '';
        if($this->isPost()) {
            $msg = "POST 请求".EOL."\n";
        }
        if($this->isGet()) {
            $msg = "GET 请求".EOL."\n";
        }

        if($this->isAjax()) {
            $msg = "Ajax 请求".EOL."\n";
        }

        All::logger('访问首页内容' ,'index');
        return $msg;
    }


    public function  testAction()
    {
        All::logger('调试日志','debug');
        All::logger(['debug'=>'调试日志'],'debug');
        return $this->request->_client_ip;
    }

    /***
     * 下面是两种发送邮件模式的对比
     * 请求参数 method  默认是task 异步任务模式
     * @return string
     */
    public function sendMailAction()
    {
        $method = isset($this->request->_get['method']) ?  $this->request->_get['method'] : 'task';

        $data['is_html'] = true;
        $data['subjcet'] = '邮件标题';
        $data['content'] = '<p>Test mail</p>';
        if($method == 'task') {
            \All::task(MailTask::class, $data);
        }else{
            $mail = \All::$app->mail;
            $mail->isSMTP();
            $mail->setFrom('963353840@qq.com', 'Mailer');
            $mail->addAddress('963353840@qq.com', 'Joe User');
            $mail->isHTML($data['is_html']);
            $mail->Subject =  $data['subjcet'];
            $mail->Body    = $data['content'];
            $mail->send();
        }
        $ret = "请求方式: $method 正在发送邮件 ";
        return $ret;
    }

}

