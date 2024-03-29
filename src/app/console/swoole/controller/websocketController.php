<?php

namespace app\console\swoole\controller;

use app\console\swoole\logic\websocketLogic;
use app\console\swoole\baseController;
use rephp\swoole\websocket\websocket;

class websocketController extends baseController
{
    /**
     * 运行websocket
     * linux:    php cmd swoole/websocket/run
     * windows:  swoole-cli cmd swoole/websocket/run
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
            exit('fail');
        }

    }
}
