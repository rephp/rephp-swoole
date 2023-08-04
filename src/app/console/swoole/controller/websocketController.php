<?php

namespace app\console\swoole\controller;

use app\console\swoole\logic\websocketLogic;
use app\swoole\baseController;
use rephp\swoole\websocket\websocket;

class websocketController extends baseController
{
    public $layout = 'index';

    /**
     * 运行websocket
     * @return void
     */
    public function runAction()
    {
        try {
            $config = [
                'websocket' => config('swoole.websocket'),
                'redis'     => config('swoole.redis'),
            ];

            $onMessageEvent = websocketLogic::getOnMessageEvent();
            $websocket      = new websocket($config, $onMessageEvent);
            $websocket->getServer()->start();
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
            echo 'Trace: ' . $e->getTraceAsString() . "\n";
            exit('');
        }

    }
}
