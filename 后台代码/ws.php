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
    R::close();
});

$server->on('message', function ($server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data}\n";
    $tmp = json_decode($frame->data,true);
    if(empty($tmp))return;
    $redis = R::getInstance();
    switch ($tmp['type']){
        case 'login':
            $uid = $frame->fd;
            $uname = $tmp['data'];
            //姓名检查
            $json = [];
            $json['from'] = 'sys';
            $json['type'] = 'nameCheck';
            if($redis->exists('uname#'.$uname)){
                $json['data'] = 'fail';
                $server->push($frame->fd , json_encode($json));
                return;
            }else{
                $json['data'] = 'success';
                $server->push($frame->fd , json_encode($json));
            }
            //初始化用户列表
            $json = [];
            $json['from'] = 'sys';
            $json['type'] = 'initUser';
            $json['data'] = [];
            $uname_list = $redis->getKeys("uname#*");
            foreach ($uname_list as $name){
                $json['data'][] = str_replace('uname#','',$name);
            }
            $server->push($frame->fd , json_encode($json));
            $redis->set('uid#'.$uid,$uname);    // uid => uname
            $redis->set('uname#'.$uname,$uid);  // uname => uid
            $json = [];
            $json['from'] = 'sys';
            $json['type'] = 'login';
            $json['data'] = $uname;
            broadcast($server,$frame->fd,'sys',json_encode($json));
            break;
        case 'private':
            $toName = $tmp['to'];
            $toId = $redis->get('uname#'.$toName);
            $fromName = $redis->get('uid#'.$frame->fd);
            $json = [];
            $json['from'] = $fromName;
            $json['type'] = 'private';
            $json['data'] = $tmp['data'];
            $server->push($toId ,json_encode($json));
            break;
        case 'public':
            $fromName = $redis->get('uid#'.$frame->fd);
            $json = [];
            $json['from'] = $fromName;
            $json['type'] = 'public';
            $json['data'] = $tmp['data'];
            broadcast($server,$frame->fd,$fromName,json_encode($json));
            break;
    }
    R::close();
});

$server->on('close', function ($server, $fd) {
    $redis = R::getInstance();
    $redis->sRemove('fdList',$fd);
    $uname = $redis->get("uid#".$fd);
    $redis->del('uid#'.$fd);
    $redis->del('uname#'.$uname);
    R::close();
    echo $uname." closed\n";
    $json = [];
    $json['from'] = 'sys';
    $json['type'] = 'logout';
    $json['data'] = $uname;
    broadcast($server,$fd,'sys',json_encode($json));
});
$server->start();
