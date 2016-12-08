# webChat
基于websocket的在线聊天系统

# 后台部分

系统要求：

php-swoole扩展

php-redis扩展

代码说明：

| 文件        | 说明                      |
| --------- | ----------------------- |
| tools.php | redis连接代码，websocket广播代码 |
| init.php  | 清理redis相关数据集的代码         |
| ws.php    | 创建websocket服务器          |

连接编号存储在redis集合中，用户名以及用户数以键值形式存储在redis中。


使用说明：

```shell
nohup /usr/bin/php /var/www/ws.php >> /dev/null 2>&1 &
```



# 前台部分

浏览器要求：

支持websocket对象

使用说明：

打开index.html页面即可
