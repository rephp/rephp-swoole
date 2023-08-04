<?php

namespace app\console\swoole\logic;

/**
 * websocket封装逻辑
 */
class websocketLogic
{
    /**
     * 自定义websocket的onmessage方法
     * @return \Closure
     */
    public static function getOnMessageEvent()
    {
        $fun = function ($fd, $message, $websocket) {
            echo '接收到消息:' . $message;
            $params = json_decode($message, true);
            $logic  = $params['logic'] ?: '';
            \app\console\swoole\logic\websocketLogic::dealMessasge($logic, $params, $fd, $websocket);
        };

        return $fun;
    }

    /**
     * 执行处理接收到的数据
     * @param \rephp\swoole\websocket\interfaces\onMessageInterface $logic     消息处理逻辑类(路由)
     * @param array                                                 $params    请求参数
     * @param mixed                                                 $fd        客户端连接句柄
     * @param \rephp\swoole\websocket\websocket                     $websocket websocket对象
     * @return mixed
     */
    public static function dealMessasge($logic, $params, $fd, $websocket)
    {
        if (empty($logic)) {
            $msg = '参数错误，没有logic，忽略:' . json_encode($params);
            echo $msg;
            $websocket->pushMsg($fd, $msg);
            return false;
        }
        return $logic::doMessasge($params, $fd, $websocket);
    }

}