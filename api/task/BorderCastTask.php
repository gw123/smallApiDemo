<?php
namespace api\task;

use core\Task;

/***
 * 向所有的websocket广播一条消息
 * Class MailTask
 * @package api\task
 */
class  BorderCastTask extends Task{

    function run()
    {
        $data = $this->data['data'];
        $group = $this->data['group'];
        $type = $this->data['type'];
        foreach (\All::$web_socket_fds  as $fd =>$value) {
            \All::push($fd, $data ,$type ,$group);
        }
    }
}
