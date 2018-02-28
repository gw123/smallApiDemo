<?php
namespace api\task;


use core\Task;

class  LoggerTask extends Task{

    function run()
    {
        $log_data = $this->data['data'];
        $group = $this->data['group'];
        //$client_ip = \All::$web_socket_fds->get($this->data['group']);
        foreach (\All::$web_socket_fds  as $fd =>$value) {
            if(in_array($value['ip'],\All::$server_config['debug_ip'])) {
                \All::push($fd, $log_data ,'log' ,$group);
            }
        }
        return '日志传送成功';
    }
}
