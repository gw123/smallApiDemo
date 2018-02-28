<?php
namespace api\controller;
use \All;
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
        return 'test';
    }
}

