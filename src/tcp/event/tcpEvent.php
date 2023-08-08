<?php

namespace rephp\swoole\tcp\event;

use rephp\swoole\tcp\tcp;

/**
 * tcp内置事件
 */
class tcpEvent
{
    /**
     * 启动tcp
     * @param tcp $websocket websocket对象
     * @return void
     */
    public static function onStart(tcp $tcp)
    {
        $config = $tcp->getConfig();
        $host   = $config['host'] ?? '127.0.0.1';
        $port   = $config['port'] ?? '9503';
        $tcp->getServer()->on('start', function ($server) use ($host, $port) {
            echo 'TCP Server is started at tcp://' . $host . ':' . $port . "\n";
        });
    }

    /**
     * 新建连接tcp
     * @param tcp $websocket websocket对象
     * @return void
     */
    public static function onConnect(tcp $tcp)
    {
        $tcp->getServer()->on('connect', function ($server, $fd) {
            echo "connection open: {$fd}\n";
        });
    }

    /**
     * 断开连接tcp
     * @param tcp $websocket websocket对象
     * @return void
     */
    public static function onClose(tcp $tcp)
    {
        $tcp->getServer()->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });
    }

    /**
     * 接收消息
     * @param tcp      $tcp tcp管理对象
     * @param \Closure $fun 回调函数
     * @return void
     */
    public static function onReceive(tcp $tcp, \Closure $fun)
    {
        $tcp->getServer()->on('receive', function ($server, $fd, $reactor_id, $data) use ($fun) {
            $fun($fd, $data, $server);
        });
    }

}