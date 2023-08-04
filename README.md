# rephp-swoole
### 一、目录信息 
1. src/app     为rephp框架加载安装rephp-swoole后要复制的应用代码

2. src/config  为rephp框架安装rephp-swoole后要复制的配置

以websocket为例，安装代码完毕后，可通过执行php cmd swoole/websocket/run来启动。


### 二、使用：
1. 所有的逻辑都放在logic文件中，每个文件中只有一个入口函数：doMessasge

2. 所有websocket客户端请求都应携带logic参数，对应logic文件名主名如demoLogic
