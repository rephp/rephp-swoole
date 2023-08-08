<?php

namespace app\console\swoole\logic;

use rephp\component\container\container;

/**
 * udp封装逻辑
 */
class udpLogic
{
    /**
     * 自定义udp-onPacket方法要触发的回调函数
     * @return \Closure
     */
    public static function getOnPacketEvent()
    {
        $fun = function ($clientAddress, $clientPort, $data, $server) {
            echo '接收到消息:' . $data . "\n";
            $params = json_decode($data, true);
            $logic  = $params['logic'] ?: '';
            \app\console\swoole\logic\udpLogic::dealMessasge($logic, $params, $clientAddress, $clientPort, $server);
        };

        return $fun;
    }

    /**
     * 分发处理路由
     * @param \rephp\swoole\udp\interfaces\onPacketInterface $logic  消息处理逻辑类(路由)
     * @param array                                           $params 请求参数
     * @param string                                             $clientAddress     客户端连接主机地址
     * @param int                                             $clientPort     客户端连接端口
     * @param \Swoole\Server                                  $server udp服务端对象
     * @return mixed
     */
    public static function dealMessasge($logic, $params, $clientAddress, $clientPort, $server)
    {
        if (empty($logic)) {
            $msg = '参数错误，没有logic，忽略:' . json_encode($params) . "\n";
            echo $msg;
            $server->sendTo($clientAddress, $clientPort, $msg);
            return false;
        }
        $logic = '\\app\\console\\swoole\\logic\\' . $logic;

        return container::getContainer()->bind($logic, $logic)->doPacket($params, $clientAddress, $clientPort, $server);
    }

}