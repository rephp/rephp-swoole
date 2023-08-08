<?php

namespace app\console\swoole\logic;

use rephp\swoole\tcp\interfaces\onReceiveInterface;

/**
 * swoole 演示tcp demo
 */
class tcpDemoLogic implements onReceiveInterface
{
    /**
     * tcp演示处理方法
     * 此tcp演示方法需要传入json请求参数{"logic":"tcpDemoLogic"}
     * 如果有多种处理入口，则需要多个logic文件，每个logic文件只有tcp请求一个入口（doReceive方法）
     * @param array $params
     * @return mixed
     */
    public static function doReceive(array $params, $fd, $server)
    {
        //推送消息
        $msg = '服务端接收到消息' . json_encode($params);
        //...do something here
        //...
        //push message to client
        $res = $server->send($fd, $msg);
        $server->close($fd);

        return $res;
    }

}