<?php

namespace rephp\swoole\tcp;

use rephp\swoole\tcp\event\tcpEvent;

class tcp
{
    /*
     * @var \Swoole\Server tcp服务端
     */
    protected $server;

    /*
     * @var array tcp配置信息
     */
    protected $config = [];

    /**
     * 实例化内置对象
     * @param array    $config         tcp配置信息
     * @param \Closure $onReceiveEvent 回调函数
     */
    public function __construct($config, \Closure $onReceiveEvent)
    {
        //实例化tcp服务端对象
        $this->createTcpServer($config ?: []);
        //初始化内置事件-websocket服务启动
        tcpEvent::onStart($this);
        tcpEvent::onConnect($this);
        tcpEvent::onClose($this);
        tcpEvent::onReceive($this, $onReceiveEvent);
    }

    /**
     * 创建tcp服务端
     * @param array $config tcp配置信息
     * @return void
     */
    protected function createTcpServer($config)
    {
        $host         = $config['host'] ?? '127.0.0.1';
        $port         = $config['port'] ?? '9503';
        $this->config = [
            'host' => $host,
            'port' => $port,
        ];
        $this->server = new \Swoole\Server($host, $port);
    }

    /**
     * 获取tcp server对象
     * @return \Swoole\Server  tcp服务端对象
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * 获取tcp配置信息
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

}

