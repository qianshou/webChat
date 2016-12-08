<?php
/**
 * Created by PhpStorm.
 * User: zhezhao
 * Date: 2016/12/7
 * Time: 22:20
 */
require 'init.php';
require 'tools.php';
$server = new swoole_websocket_server("0.0.0.0", 9501);
$server->on('open', function ($server, $request) {
    $redis = R::getInstance();
    $redis->sAdd('fdList',$request->fd);
    $redis->incr('fdCounter');
    R::close();
});

$server->on('message', function ($server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data}\n";
    $tmp = json_decode($frame->data,true);
    if(empty($tmp))return;
    $redis = R::getInstance();
    switch ($tmp['type']){
        case 'name':
            $redis->set("user".$frame->fd,$tmp['data']);
            $counter = $redis->get('fdCounter');
            $msg = $tmp['data'].'已登录，当前'.$counter.'用户在线';
            broadcast($server,'sys','all',$msg);
            break;
        case 'msg':
            broadcast($server,$frame->fd,'all',$tmp['data']);
            break;
    }
    R::close();
});

$server->on('close', function ($server, $fd) {
    echo $fd." closed\n";
    $redis = R::getInstance();
    $redis->sRemove('fdList',$fd);
    $redis->del("user".$fd);
    $redis->decr('fdCounter');
    R::close();
    $msg = $redis->get("user".$fd)."已退出";
    broadcast($server,'sys','all',$msg);
});
$server->start();
