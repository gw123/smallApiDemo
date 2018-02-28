<?php
namespace api\controllerWs;

use \All;
use core\WsController;

class  IndexController extends WsController
{
    public  function  indexAction()
    {
        $data['session']= $this->request->getSessionId();
        return  $data;
    }
}

