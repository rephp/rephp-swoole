<?php

namespace app\console\swoole\logic;

use rephp\swoole\udp\interfaces\onPacketInterface;

/**
 * swoole 演示udp demo
 */
class udpDemoLogic implements onPacketInterface
{
    /**
     * udp演示处理方法
     * 此udp演示方法需要传入json请求参数{"logic":"udpDemoLogic"}
     * 如果有多种处理入口，则需要多个logic文件，每个logic文件只有udp请求一个入口（doPacket方法）
     * @param array $params
     * @return mixed
     */
    public static function doPacket(array $params, $clientAddress, $clientPort, $server)
    {
        //推送消息
        $msg = '服务端接收到消息' . json_encode($params);
        //...do something here
        //...
        //push message to client
        $res = $server->sendTo($clientAddress, $clientPort, $msg);

        return $res;
    }

}