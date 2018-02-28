![image](https://github.com/gw123/smallApiDemo/blob/master/smallApi.png?raw=true)
##基于swoole 一个高性能的轻量级框架
[![Latest Version](https://img.shields.io/badge/unstable-v1.0-yellow.svg?maxAge=2592000)](https://packagist.org/packages/swoft/swoft)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg?maxAge=2592000)]()

#目录说明
![image](https://github.com/gw123/smallApiDemo/blob/master/%E6%A1%86%E6%9E%B6%E8%AF%B4%E6%98%8E%E5%9B%BE.png?raw=true)

# 简介
基于swoole实现的高性能简单实用框架。

- 基于redis实现的session机制，方便实现多实例，多项目协同开发
- 实现websocket 和http会话同步机制 ，在websocket断线重连后可以方便的恢复原来的会话
- 仿造Yii实现Di容器，在使用组件时更加方便友好，方便自己扩展框架
- 基于Yii框架Model，提供良好安全的CURD ,AR模型
- MVC 分层设计，除了http请求websocket 请求也实现了MC机制方便编程，提高代码的可读性
- 配置文件可以动态和静态加载，提供基于redis动态加载配置文件方式，基于内置的debug调试器可以随时修改动态配置
- 提供基于浏览器的web调试工具，可以实时查看服务器日志，监控进程的运行状态，动态修改配置
- 异步任务支持，可以摆脱传统框架对消息队列的依赖，轻松实现高负载
- 框架代码提供大量的注释帮助大家理解，使用phpstrom框架所有类和方法可以补全和提示

#安装 
- 直接 git clone git@github.com:gw123/smallApiDemo.git
- 修改根目录下的脚本文件 smallApi 修改为自己的安装位置
```
prefix=/data/wwwroot/smallApi  #项目安装目录
php_bin=/data/install/php/bin/php #php执行文件位置
app_entry=${prefix}/api/api.php   #入口脚本位置
app_pid_file=/var/run/swoole.pid  #pid文件
```
#运行
./smallApi start 启动服务
./smallApi stop 关闭服务
./smallApi reload 热重启服务，类似nginx reload
./smallApi restart 请求服务
./smallApi check 检测服务是否正常，这个命令可以配合一个 定时脚本用来检测服务是否正常，服务异常可以自动重启

可以将脚本放到 /etc/init.d目录下面 配合 chkconfig  命令实现开机自动运行
chkconfig --add smallApi

#debug调试器
[![Latest Version](https://img.shields.io/badge/unstable-v1.0-yellow.svg?maxAge=2592000)]
[![Latest Version](https://img.shields.io/badge/unstable-v1.0-yellow.svg?maxAge=2592000)]
[![Latest Version](https://img.shields.io/badge/unstable-v1.0-yellow.svg?maxAge=2592000)]
[![Latest Version](https://img.shields.io/badge/unstable-v1.0-yellow.svg?maxAge=2592000)]

#异步任务
对应代码在 api/controller/IndexController.php sendMailAction() 
在这段代码中展示了使用异步任务发送邮件和使用同步任务发送邮件的区别。

- 首先在api/config/config.php 指定邮件组件的账号信息
```
 'mail'=>array(
                'class'=>\PHPMailer\PHPMailer\PHPMailer::class,
                'Host'=>'smtp.qq.com',
                'SMTPAuth' => true,
                'Username' => '1234@qq.com',
                'Password' => '***',
                'SMTPSecure' => 'ssl',
                'Port'=> '465',
            ),
```

- 发送邮件
```
/*
 * 异步发送邮件
 */
$data['is_html'] = true;
$data['subjcet'] = '邮件标题';
$data['content'] = '<p>Test mail</p>';
\All::task(MailTask::class, $data);

```
下面是两种发送方式耗时对比


