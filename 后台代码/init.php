<?php
/**
 * Created by PhpStorm.
 * User: zhezhao
 * Date: 2016/12/8
 * Time: 9:40
 */
$redis = new Redis();
$redis->connect('210.73.27.35', 6379);
$redis->del('fdCounter','fdList');
$user_list = $redis->getKeys("user*");
foreach ($user_list as $user){
    $redis->del($user);
}
$redis->close();
