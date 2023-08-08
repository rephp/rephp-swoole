<?php

namespace app\console\swoole\controller;

use app\console\swoole\baseController;
use app\console\swoole\logic\tcpLogic;
use rephp\swoole\tcp\tcp;

class tcpController extends baseController
{
    /**
     * 运行tcp
     * linux:    php cmd swoole/tcp/run
     * windows:  swoole-cli cmd swoole/tcp/run
     * @return void
     */
    public function runAction()
    {
        try {
            $config         = [
                'tcp' => config('swoole.tcp'),
            ];
            $onReceiveEvent = tcpLogic::getOnReceiveEvent();
            $tcp            = new tcp($config, $onReceiveEvent);
            $tcp->getServer()->start();
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
            echo 'Trace: ' . $e->getTraceAsString() . "\n";
            exit('fail');
        }

    }
}
