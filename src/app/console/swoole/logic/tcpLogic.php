<?php

namespace app\console\swoole\logic;

use rephp\component\container\container;

/**
 * tcp封装逻辑
 */
class tcpLogic
{
    /**
     * 自定义tcp-onReceive方法要触发的回调函数
     * @return \Closure
     */
    public static function getOnReceiveEvent()
    {
        $fun = function ($fd, $data, $server) {
            echo '接收到消息:' . $data . "\n";
            $params = json_decode($data, true);
            $logic  = $params['logic'] ?: '';
            \app\console\swoole\logic\tcpLogic::dealMessasge($logic, $params, $fd, $server);
        };

        return $fun;
    }

    /**
     * 分发处理路由
     * @param \rephp\swoole\tcp\interfaces\onReceiveInterface $logic  消息处理逻辑类(路由)
     * @param array                                           $params 请求参数
     * @param int                                             $fd     客户端连接句柄
     * @param \Swoole\Server                                  $server tcp服务端对象
     * @return mixed
     */
    public static function dealMessasge($logic, $params, $fd, $server)
    {
        if (empty($logic)) {
            $msg = '参数错误，没有logic，忽略:' . json_encode($params) . "\n";
            echo $msg;
            $server->send($fd, $msg);
            $server->close($fd);

            return false;
        }
        $logic = '\\app\\console\\swoole\\logic\\' . $logic;

        return container::getContainer()->bind($logic, $logic)->doReceive($params, $fd, $server);
    }

}