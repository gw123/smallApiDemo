<?php
namespace api\controller;

use \All;
use api\task\CountTask;
use core\Controller;

class DebugController extends Controller
{
    /***
     * 系统监控页面
     * @return string
     */
    public function indexAction()
    {
        return $this->render('/debug/debug');
    }

    /***
     * 获取所有的动态配置的参数
     * @return array
     */
    public function configAction()
    {
        $params = All::$app->redis->hGetAll('dynamic_params');
        return $this->success($params);
    }

    /***
     * 修改动态配置参数
     * @return string
     */
    public function modifyConfigAction()
    {
       $post =  $this->request->_post;
        foreach ($post as $key => $value) {
            All::$app->redis->hSet('dynamic_params', $key, $value);
        }
        return  $this->success([], '修改成功');
    }

    /***
     * 手动重启worker 进程
     * @return string
     */
    public function restartAction()
    {
        All::$channel->push('reload');
        return $this->asJson(['msg' => '正在重启']);
    }

    /***
     * phpinfo
     * @return string
     */
    public function phpinfoAction()
    {
        ob_start();
        phpinfo();
        $php_info = ob_get_clean();
        return $this->success($php_info);
    }

    /***
     * sysinfo
     * @return string
     */
    public function sysinfoAction()
    {
        $sys_info  = All::$app->redis->get('sysinfo');
        if($sys_info) {
            $sys_info = json_decode($sys_info, true);
        } else {
            exec('top -n 1 -b -c',$sys_info);
        }

        $total_info = array_splice($sys_info,0,7);
        $programs =[];

        foreach ($sys_info as $line) {
            $line = trim($line);
            $line = str_replace('  ',' ',$line);
            $line = str_replace('  ',' ',$line);
            $line = str_replace('  ',' ',$line);
            $params = explode(' ',$line);
            $temp = array_splice($params,0,11);
            $cmd  = implode(' ',$params);

            if(preg_match('/\[.*\]/',$cmd)) {
                continue;
            }
            if(preg_match('/^(top|bash|-bash|sshd|ps|su|ls|cd|cp|mv|sleep|.*ibus-|.*dbus-).*/',$cmd)) {
                continue;
            }
            $temp[] = $cmd;
            $programs[] = $temp;
        }

        unset($sys_info);
        $header = array_splice($total_info,6,1);
        array_splice($total_info,5,1);
        $header = trim($header[0]);
        $header = str_replace('  ',' ',$header);
        $header = str_replace('  ',' ',$header);
        $header = str_replace('  ',' ',$header);
        $header = explode(' ',$header);

        $sys_info['total'] = $total_info;
        $sys_info['header'] = $header;
        $sys_info['body'] = $programs;

        return $this->success($sys_info);
    }

    /***
     * 查看当前web socket 连接数量
     * @return string
     */
    public function getWsListAction()
    {
        $fds = [];
        foreach (All::$web_socket_fds as $fd => $value) {
            $temp['session_id'] = $value['session_id'];
            $temp['is_debug'] = $value['is_debug'];
            $temp['fd'] = $fd;
            $fds[] = $temp;
        }
        return $this->asJson($fds);
    }

    function getIPAction()
    {
        $data['class'] = CountTask::class;
        All::$server->task($data);
        return $this->request->_client_ip;
    }

}

