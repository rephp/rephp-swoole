<?php

namespace app\modules\index\controller;

use app\swoole\baseController;

class websocketController extends baseController
{
    public $layout = 'index';

    public function runAction()
    {
        try {
            $config = [
                'websocket' => config('swoole.websocket'),
                'redis'     => config('swoole.redis'),
            ];
            //动态绑定组件
            //1.初始化server对象
            //2.加载动态事件
            //3.运行
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
            echo 'Trace: ' . $e->getTraceAsString() . "\n";
            exit('');
        }


    }
}
