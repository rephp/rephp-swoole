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
        $redis           = $websocket->getRedis();
        $websocket->getServer()->on('start', function ($server) use ($host, $port, $redis) {
            //1.del room cache
            $roomKeyArr = $redis->keys('swoole:websocket:room:*');
            empty($roomKeyArr) && $roomKeyArr = [];
            foreach($roomKeyArr as $roomKey){
                $redis->del($roomKey);
            }
            //2.del all user cache
            $redis->del('swoole:websocket:user');
            echo 'Websocket Server is started at ws://' . $host . ':' . $port . "\n";
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
            $redis->hset('swoole:websocket:user', $req->fd, $roomId);
            $redis->hset('swoole:websocket:room:room_'.$roomId, $req->fd, $roomId);
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
            $roomId = $redis->hget('swoole:websocket:user', $fd) ?: 0;
            $redis->hdel('swoole:websocket:room:room_'.$roomId, $fd);
            $redis->hdel('swoole:websocket:user', $fd);
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