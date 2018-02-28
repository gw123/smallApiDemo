<?php
namespace api\task;

use core\Task;

/***
 * 发送短信的任务
 * Class MessageTask
 * @package api\task
 */
class MessageTask extends Task{

    function run()
    {
        $log_data = $this->data['data'];
        $group = $this->data['group'];
        foreach (\All::$web_socket_fds  as $fd =>$value) {
            if(in_array($value['ip'],\All::$server_config['debug_ip'])) {
                \All::push($fd, $log_data ,'log' ,$group);
            }
        }
        return '日志传送成功';
    }
}
