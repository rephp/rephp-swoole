<?php

namespace rephp\swoole\udp;

use rephp\swoole\udp\event\udpEvent;

class udp
{
    /*
     * @var \Swoole\Server udp服务端
     */
    protected $server;

    /*
     * @var array udp配置信息
     */
    protected $config = [];

    /**
     * 实例化内置对象
     * @param array    $config         udp配置信息
     * @param \Closure $onReceiveEvent 回调函数
     */
    public function __construct($config, \Closure $onPacketEvent)
    {
        //实例化udp服务端对象
        $this->createUdpServer($config ?: []);
        //初始化内置事件-udp服务启动
        udpEvent::onStart($this);
        udpEvent::onPacket($this, $onPacketEvent);
    }

    /**
     * 创建udp服务端
     * @param array $config udp配置信息
     * @return void
     */
    protected function createUdpServer($config)
    {
        $host         = $config['host'] ?? '127.0.0.1';
        $port         = $config['port'] ?? '9504';
        $this->config = [
            'host' => $host,
            'port' => $port,
        ];
        $this->server = new \Swoole\Server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
    }

    /**
     * 获取udp server对象
     * @return \Swoole\Server  udp服务端对象
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * 获取udp配置信息
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

}

