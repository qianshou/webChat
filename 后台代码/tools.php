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
function broadcast($server,$from,$to,$data){
    $redis = R::getInstance();
    $fd_list = $redis->sMembers('fdList');
    $username = ($from == 'sys')? 'sys':$redis->get("user".$from);
    $msg = '{"from":"'.$username.'","to":"'.$to.'","data":"'.$data.'"}';;
    echo "broadcast ".$msg."\n";
    R::close();
    foreach ($fd_list as $fd){
        if($fd == $from)continue;
        $server->push($fd , $msg);
    }
}