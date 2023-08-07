# rephp-swoole
### 一、目录信息 
1. src/app     为rephp框架加载安装rephp-swoole后要复制的应用代码

2. src/config  为rephp框架安装rephp-swoole后要复制的配置

### 二、运行
以websocket为例，安装代码完毕后，可通过执行:

1. linux系统  php cmd swoole/websocket/run

2. windows系统  swoole-cli cmd swoole/websocket/run

### 三、应用：
1. 所有的逻辑都放在logic文件中，每个文件中只有一个入口函数：doMessasge

2. 所有websocket客户端请求都应携带logic参数，对应logic文件名主名如demoLogic

### 三、请求：
1. 以websocket为例，根据配置连接websocket服务端。默认服务端地址为: ws://127.0.0.1:9502
2. 发送指定格式json信息为内容到websocket服务端。
       