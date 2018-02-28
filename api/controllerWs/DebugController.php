<?php
namespace api\controllerWs;
use \All;
use core\WsController;


class  DebugController extends WsController
{
    public function  beginAction()
    {
        $fd = $this->request->_fd;
        $session_id = $this->request->getSessionId();
        $data['session_id'] = $session_id;
        $data['is_debug'] = 1;
        All::$web_socket_fds->set($fd , $data);
        return ['msg'=>'认证成功'];
    }

}

