<?php

namespace rephp\swoole\udp\interfaces;

/**
 * udp logic使用的公共接口
 */
interface onPacketInterface
{
    /**
     * 基于swoole的udp监听接收消息处理方法接口
     * @param array          $params        请求参数
     * @param string         $clientAddress 客户端连接地址
     * @param int            $clientPort    客户端连接端口
     * @param \Swoole\Server $server        udp服务端对象
     * @return mixed
     */
    public static function doPacket(array $params, $clientAddress, $clientPort, $server);

}