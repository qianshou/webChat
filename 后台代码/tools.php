<?php
/**
 * Created by PhpStorm.
 * User: zhezhao
 * Date: 2016/12/8
 * Time: 9:49
 */
class R {
    private static $instance = null;
    public static $name;
    public static function getInstance(){
        if(! self::$instance instanceof Redis){
            self::$instance = new Redis();
            self::$instance->connect('210.73.27.35', 6379);
        }
        return self::$instance;
    }
    private function __construct(){}
    private function __clone(){}
    public static function close(){
        if(! empty(self::$instance)){
            self::$instance->close();
        }
        self::$instance = null;
    }
}
function broadcast($server,$cfd,$from,$json){
    $redis = R::getInstance();
    $fd_list = $redis->sMembers('fdList');
    if($from == 'sys'){
        //系统消息，无差别发送
        foreach ($fd_list as $fd){
            $server->push($fd,$json);
        }
    }else{
        //public消息，不发送给cfd
        foreach ($fd_list as $fd){
            if($fd == $cfd)continue;
            $server->push($fd,$json);
        }
    }
    R::close();
}