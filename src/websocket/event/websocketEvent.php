<?php

namespace rephp\swoole\websocket\event;

use rephp\swoole\websocket\websocket;

/**
 * websocket内置事件
 */
class websocketEvent
{
    /**
     * 启动websocket
     * @param websocket $websocket websocket对象
     * @return void
     */
    public static function onStart(websocket $websocket)
    {
        $websocketConfig = $websocket->getWebsocketConfig();
        $host            = $websocketConfig['host'] ?? '127.0.0.1';
        $port            = $websocketConfig['port'] ?? '9502';
        $websocket->getServer()->on('start', function ($server) use ($host, $port) {
            echo "Websocket Server is started at ws://'.$host.':'.$port.'\n";
        });
    }

    /**
     * 用户建立连接登录
     * @param websocket $websocket websocket对象
     * @return void
     */
    public static function onOpen(websocket $websocket)
    {
        $redis = $websocket->getRedis();
        $websocket->getServer()->on('open', function ($server, $req) use ($redis) {
            $roomId = $req->room_id ?? 0;
            $redis->set('websocket:user:' . $req->fd, $roomId);
            $redis->hset('websocket:room_' . $roomId, $req->fd, 1);
            echo "connection open: {$req->fd}\n";
        });
    }

    /**
     * 用户退出关闭连接操作
     * @param websocket $websocket websocket对象
     * @return void
     */
    public static function onClose(websocket $websocket)
    {
        $redis = $websocket->getRedis();
        $websocket->getServer()->on('close', function ($server, $fd) use ($redis) {
            $roomId = $redis->get('websocket:user:' . $fd) ?: 0;
            $redis->hdel('websocket:room_' . $roomId, $fd);
            $redis->del('websocket:user:' . $fd);
            echo "connection close: {$fd}\n";
        });
    }

    /**
     * 监听接收消息事件
     * @param websocket $websocket websocket对象
     * @param \Closure  $fun
     * @return void
     */
    public static function onMessage(websocket $websocket, \Closure $fun)
    {
        $websocket->getServer()->on('message', function ($server, $frame) use ($fun, $websocket) {
            $fun($frame->fd, $frame->data, $websocket);
        });
    }
}