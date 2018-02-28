<?php

/* 数据库配置 */
$config =  array(
        'params'=> require (APP_PATH.'/config/param.php'),
        'components'=>[
            'sphinx' => array(
                'class' => '\yii\db\Connection',
                'dsn' => 'mysql:host=192.168.30.1;dbname=edu',
                'username' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
            ),
            'db' => array(
                 'class' => '\yii\db\Connection',
                 'dsn' => 'mysql:host=192.168.30.1;dbname=edu',
                 'username' => 'root',
                 'password' => 'root',
                 'charset' => 'utf8',
            ),
            'db_user' => array(
                'class' => '\yii\db\Connection',
                'dsn' => 'mysql:host=192.168.30.1;dbname=user',
                'username' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
            ),
            'redis'=>array(
                'class'=>core\redis\RedisClient::class,
                'host'=>'127.0.0.1',
                'port'=>6379,
                //'auth'=>'gao123456'
            ),
            'mail'=>array(
                'class'=>\PHPMailer\PHPMailer\PHPMailer::class,
                'Host'=>'smtp.qq.com',
                'SMTPAuth' => true,
                'Username' => '963353840@qq.com',
                'Password' => 'szwcgykrerfrbfbb',
                'SMTPSecure' => 'ssl',
                'Port'=> '465',
            ),
            'view'=> array(
                'class'=> core\view\TwigView::class,
                'cache'=>false,
                'twigConfig'=>[
                    'debug'=>true,
                    'charset'=>'utf-8',
                    'cache '=> ROOT_PATH."/runtime/view",
                    'auto_reload'=>true,
                    'optimizations '=>'-1',//优化方式的标志(-1，启用所有优化; 0禁用)
                ]
            )
        ]
);

return $config;