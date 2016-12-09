<?php
/**
 * Created by PhpStorm.
 * User: zhezhao
 * Date: 2016/12/8
 * Time: 9:40
 */
$redis = new Redis();
$redis->connect('210.73.27.35', 6379);
$uid_list = $redis->getKeys("uid#*");
foreach ($uid_list as $id){
    $redis->del($id);
}
$uname_list = $redis->getKeys("uname#*");
foreach ($uname_list as $name){
    $redis->del($name);
}
$redis->del('fdList');
$redis->close();
