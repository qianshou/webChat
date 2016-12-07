<?php
/**
 * Created by PhpStorm.
 * User: zhezhao
 * Date: 2016/12/7
 * Time: 22:20
 */
$server = new swoole_websocket_server("0.0.0.0", 9501);
$server->on('open', function ($server, $request) {
    //计数器加1
    $i = intval(file_get_contents('ct.txt'));
    $i++;
    file_put_contents('ct.txt',$i);
    //读取fd队列
    $tmp = trim(file_get_contents('fd.txt'),',');
    if($tmp){
        $fd_list = explode(',',$tmp);
    }else{
        $fd_list = [];
    }
    array_push($fd_list,$request->fd);
    file_put_contents('fd.txt',$request->fd.',',FILE_APPEND);
    //所有用户进行广播
    foreach ($fd_list as $fd){
       $server->push(intval($fd),$i.' users online');
       $server->push(intval($fd),'User '.$request->fd." login.");
    }
});

$server->on('message', function ($server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data}\n";
    $tmp = trim(file_get_contents('fd.txt'),',');
    $fd_list = explode(',',$tmp);
    //所有用户进行广播
    foreach ($fd_list as $fd){
        $server->push(intval($fd),'User '.$frame->fd." say:".$frame->data);
    }
});

$server->on('close', function ($server, $cfd) {
    echo $cfd." closed\n";
    $tmp = trim(file_get_contents('fd.txt'),',');
    $fd_list = explode(',',$tmp);
    $tmp = str_replace($cfd.',','',$tmp);
    file_put_contents('fd.txt',$tmp);
    //所有用户进行广播
    foreach ($fd_list as $fd){
        if($fd == $cfd)continue;
        $server->push(intval($fd),'User '.$cfd." logout.");
    }

});

$server->start();
