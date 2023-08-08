<?php
namespace app\console\swoole\logic;

use rephp\swoole\websocket\interfaces\onMessageInterface;

/**
 * swoole 演示websocket demo
 */
class websocketDemoLogic implements onMessageInterface
{
    /**
     * websocket演示处理方法
     * 此websocket演示方法需要传入json请求参数{"logic":"websocketDemoLogic"}
     * 如果有多种处理入口，则需要多个logic文件，每个logic文件只有websocket请求一个入口（doMessasge方法）
     * @param array $params
     * @return mixed
     */
    public static function doMessasge(array $params, $fd, $websocket)
    {
        //推送消息
        $msg = '服务端接收到消息' . json_encode($params);
        //...do something here
        //...
        //push message to client
        $res = $websocket->pushMsg($fd, $msg, 'room');

        return $res;
    }

}