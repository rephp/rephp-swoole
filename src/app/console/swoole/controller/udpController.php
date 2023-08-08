<?php

namespace app\console\swoole\controller;

use app\console\swoole\baseController;
use app\console\swoole\logic\udpLogic;
use rephp\swoole\udp\udp;

class udpController extends baseController
{
    /**
     * 运行udp
     * linux:    php cmd swoole/udp/run
     * windows:  swoole-cli cmd swoole/udp/run
     * @return void
     */
    public function runAction()
    {
        try {
            $config        = config('swoole.udp') ?? [];
            $onPacketEvent = udpLogic::getOnPacketEvent();
            $udp           = new udp($config, $onPacketEvent);
            $udp->getServer()->start();
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
            echo 'Trace: ' . $e->getTraceAsString() . "\n";
            exit('fail');
        }

    }
}
