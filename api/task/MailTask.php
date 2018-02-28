<?php
namespace api\task;

use core\Task;

/***
 * 发送邮件任务
 * example:
 *  $data['is_html'] = true;
 *  $data['subjcet'] = '邮件标题';
 *  $data['content'] = '<p>Test mail</p>';
 *  \All::task(MailTask::class,$data);
 *
 * Class MailTask
 * @package api\task
 */
class  MailTask extends Task{

    function run()
    {
        /***
         * @var $mail \PHPMailer\PHPMailer\PHPMailer::class
         */
        $mail = \All::$app->mail;
        if(isset($this->data['subjcet'])) {
            $subject = $this->data['subjcet'] ;
        } else {
            $subject = $this->data['subjcet'] ;
        }

        if(isset($this->data['is_html'])) {
            $is_html = $this->data['is_html'] ;
        }else{
            $is_html = false;
        }

        $content = '';
        if(isset($this->data['content'])) {
            $content = $this->data['content'];
        }else{
            \All::logger('邮件发送失败 缺少发送的内容 content','mail');
            return '发送失败';
        }

        try {
            $mail->isSMTP();
            $mail->setFrom('963353840@qq.com', 'Mailer');
            $mail->addAddress('963353840@qq.com', 'Joe User');
            $mail->isHTML($is_html);
            $mail->Subject = $subject;
            $mail->Body    = $content;
            $mail->send();
        }catch (\Exception $e){
            \All::logger('邮件发送失败','mail');
        }

    }
}
