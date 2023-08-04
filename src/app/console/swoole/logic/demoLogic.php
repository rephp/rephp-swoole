<?php
namespace app\console\swoole\logic;

use rephp\swoole\websocket\interfaces\onMessageInterface;

/**
 * swoole websocket演示demo
 */
class demoLogic implements onMessageInterface
{
    /**
     * 演示公共处理方法
     * @param array $params
     * @return mixed
     */
    public static function doMessasge(array $params, $fd, $websocket)
    {
        //推送消息
        $msg = '服务端接收到消息' . json_encode($params);
        return $websocket->pushMsg($fd, $msg, 'room');
    }

}