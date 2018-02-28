<?php
/****
 * 这里记录的是一些可以动态配置的内容
 * 这里的值在第一次加载时会写入到redis中，当redis存在key时不加载
 */

return  [
    'is_debug' => true,
    'show_sys_log' => true,
    'worker_num' => 2,
    'max_request' => 1,
    'task_worker_num' => 4,
    'host' => 'api.17ky.xyt',
    'token' => 'wmaker',
    'qiniu_accesskey' => '***',
    'qiniu_secretKey' => '***',
    'qiniu_bucketName' => 'speech',
    'qiniu_download_host' => 'http://***.clouddn.com',
    'qiniu_download_host2' => 'http://***.clouddn.com',
    'wx_appid' => 'wx***',
    'wx_secret' => '***',

    'bd_ocr_appid'=>'',
    'bd_ocr_appkey'=>'',
    'bd_ocr_secretkey'=>'',
    'bd_speech_appid'=>'',
    'bd_speech_appkey'=>'',
    'bd_spech_secretkey'=>'',

];
