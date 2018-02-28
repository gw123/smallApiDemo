<?php
namespace api\task;


use core\Task;

class  CountTask extends Task{

    function run()
    {
        \All::$app->redis->incr(R_Total_PV);
        return \All::$app->redis->get(R_Total_PV);
    }
}
