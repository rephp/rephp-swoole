<?php

namespace rephp\swoole\websocket;

use rephp\swoole\websocket\interfaces\websocketInterface;

/**
 * todo:思路内置对象，对外开放简单方法如推送和接收数据并执行方法（这个放在引用模块）
 * 1.连接
 * 2.接收
 * 3.发送
 * 4.初始化基础信息
 */
class websocket implements websocketInterface
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
    public function __construct($config)
    {
        //实例化redis对象
        $this->createRedis($config['redis'] ?? []);
        //实例化websocket服务端对象
        $this->createWebsocketServer($config['websocket'] ?? []);
        //初始化内置事件-websocket服务启动
        $this->onStart();

    }

    /**
     * 实例化redis对象
     * @param array $config redis配置信息
     * @return void
     */
    protected function createRedis($config)
    {
        $this->redisConfig = $config;
        //todo:连接redis
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
     * 启动websocket
     * @param \Swoole\Websocket\Server $server websocket连接对象
     * @return void
     */
    protected function onStart()
    {
        $this->server->on('start', function ($server) {
            echo "Websocket Server is started at ws://'.$this->websocketConfig['host'].':'.$this->websocketConfig['port'].'\n";
        });
    }

    /**
     * 用户建立连接登录
     * @return void
     */
    protected function onOpen()
    {
        $redis = $this->redis;
        $this->server->on('open', function ($server, $req) use ($redis) {
            $roomId = $req->room_id ?? 0;
            $redis->set('websocket:user:' . $req->fd, $roomId);
            $redis->hset('websocket:room_' . $roomId, $req->fd, 1);
            echo "connection open: {$req->fd}\n";
        });
    }

    /**
     * 监听接收消息事件
     * @param          $server
     * @param \Closure $fun
     * @return void
     */
    public function onMessage($server, \Closure $fun)
    {
        $server->on('message', function ($server, $frame) use ($fun) {
            echo "received message: {$frame->data}\n";
            $fun($frame->fd, $frame->data);
        });
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
        if(empty($fdArr)){
            return false;
        }
        is_string($msg) || $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        foreach ($fdArr as $sendFd) {
            $this->server->push($sendFd, $msg);
        }

        return true;
    }

    /**
     * 用户退出关闭连接操作
     * @return void
     */
    protected function onClose()
    {
        $redis = $this->redis;
        $this->server->on('close', function ($server, $fd) use ($redis) {
            $roomId = $redis->get('websocket:user:' . $fd) ?: 0;
            $redis->hdel('websocket:room_' . $roomId, $fd);
            $redis->del('websocket:user:' . $fd);
            echo "connection close: {$fd}\n";
        });
    }
}
