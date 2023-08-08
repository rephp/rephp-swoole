<?php

namespace rephp\swoole\udp\event;

use rephp\swoole\udp\udp;

/**
 * udp内置事件
 */
class udpEvent
{
    /**
     * 启动
     * @param udp $udp udp对象
     * @return void
     */
    public static function onStart(udp $udp)
    {
        $config = $udp->getConfig();
        $host   = $config['host'] ?? '127.0.0.1';
        $port   = $config['port'] ?? '9503';
        $udp->getServer()->on('start', function ($server) use ($host, $port) {
            echo 'UDP Server is started at udp://' . $host . ':' . $port . "\n";
        });
    }

    /**
     * 接收消息
     * @param udp      $udp udp管理对象
     * @param \Closure $fun 回调函数
     * @return void
     */
    public static function onPacket(udp $udp, \Closure $fun)
    {
        $udp->getServer()->on('packet', function ($server, $data, $clientInfo) use ($fun) {
            $clientAddress = $clientInfo['address'] ?? '';
            $clientPort    = $clientInfo['port'] ?? 0;
            $fun($clientAddress, $clientPort, $data, $server);
        });
    }

}