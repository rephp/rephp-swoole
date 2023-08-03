<?php

namespace rephp\swoole\websocket\interfaces;
interface websocketInterface
{
    public function onMessage($server, \Closure $fun);
}