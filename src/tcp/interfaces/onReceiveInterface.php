<?php

namespace rephp\swoole\tcp\interfaces;

interface onReceiveInterface
{
    /**
     * 基于swoole的tcp监听接收消息处理方法接口
     * @param array          $params 请求参数
     * @param int            $fd     客户端连接句柄
     * @param \Swoole\Server $server tcp服务端对象
     * @return mixed
     */
    public static function doReceive(array $params, $fd, $server);

}