<?php

namespace rephp\swoole\websocket\interfaces;

/**
 * websocket logic使用的公共接口
 */
interface onMessageInterface
{
    /**
     * 基于swoole的websocket监听接收消息处理方法接口
     * @param array                             $params    请求参数
     * @param int                               $fd        客户端连接句柄
     * @param \rephp\swoole\websocket\websocket $websocket websocket对象
     * @return mixed
     */
    public static function doMessasge(array $params, $fd, $websocket);

}