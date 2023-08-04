<?php

namespace rephp\swoole\websocket;

use rephp\swoole\websocket\event\websocketEvent;

/**
 * todo:思路内置对象，对外开放简单方法如推送和接收数据并执行方法（这个放在引用模块）
 * 1.连接
 * 2.接收
 * 3.发送
 * 4.初始化基础信息
 */
class websocket
{
    /*
     * @var \Swoole\Websocket\Server $server websocket服务端对象
     */
    protected $server;
    /*
     * @var \Redis $redis redis客户端对象
     */
    protected $redis;

    /*
     * @var array $websocketConfig websocket配置信息
     */
    protected $websocketConfig;

    /*
     * @var array $redisConfig  redis配置信息
     */
    protected $redisConfig;

    /**
     * 实例化内置对象
     */
    public function __construct($config, \Closure $onMessageEvent)
    {
        //实例化redis对象
        $this->createRedis($config['redis'] ?? []);
        //实例化websocket服务端对象
        $this->createWebsocketServer($config['websocket'] ?? []);
        //初始化内置事件-websocket服务启动
        websocketEvent::onStart($this);
        websocketEvent::onOpen($this);
        websocketEvent::onClose($this);
        websocketEvent::onMessage($this, $onMessageEvent);
    }

    /**
     * 服务端发送信息
     * @param mixed        $fd   当前websocket连接信息
     * @param string|array $msg  要发送的消息内容
     * @param string       $type 发送类型:all=系统全局通知,room=当前房间内公告,p2p=给指定当前连接人发送信息
     * @return boolean
     */
    public function pushMsg($fd, $msg, $type = 'p2p')
    {
        switch ($type) {
            case 'room'://当前房间内公告通知
                $roomId = $this->redis->get('websocket:user:' . $fd) ?: 0;
                $fdArr  = $this->redis->hKeys('websocket:room_' . $roomId);
                break;
            case 'all'://系统通知，所有建立连接的人
                $fdArr = $this->redis->keys('websocket:room_' . '*');
                break;
            case 'p2p'://给指定人发送连接
            default:
                $fdArr = [$fd];
                break;
        }
        //开始逐个推送
        $result = false;
        if (!empty($fdArr)) {
            is_string($msg) || $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            foreach ($fdArr as $sendFd) {
                $this->server->push($sendFd, $msg);
            }
            $result = true;
        }

        return $result;
    }

    /**
     * 实例化redis对象
     * @param array $config redis配置信息
     * @return void
     */
    protected function createRedis($config)
    {
        $this->redisConfig = $config;
        $host              = $config['host'] ?: '127.0.0.1';
        $port              = $config['port'] ?: '6379';
        $connectTimeout    = $config['connect_timeout'] ?: 60;
        $password          = $config['password'] ?: '';
        $redis             = new \Redis();
        $redis->connect($host, $port, $connectTimeout);
        empty($password) || $redis->auth($password);

        $this->redis = $redis;
    }

    /**
     * 实例化websocket服务端对象
     * @param array $config websocket配置信息
     * @return void
     */
    protected function createWebsocketServer($config)
    {
        $this->websocketConfig = $config;
        $host                  = $this->websocketConfig['host'] ?: '127.0.0.1';
        $port                  = $this->websocketConfig['port'] ?: 9502;
        $this->server          = new \Swoole\Websocket\Server($host, $port);
    }

    /**
     * 获取redis配置信息
     * @return array|null
     */
    public function getRedisConfig()
    {
        return $this->redisConfig;
    }

    /**
     * 获取websocket配置信息
     * @return array|null
     */
    public function getWebsocketConfig()
    {
        return $this->websocketConfig;
    }

    /**
     * 获取redis对象
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * 获取websocket server对象
     * @return \Swoole\Websocket\Server
     */
    public function getServer()
    {
        return $this->server;
    }

}
