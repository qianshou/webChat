# webChat
基于websocket的在线聊天系统

# 后台部分

## 系统要求

php-swoole扩展

php-redis扩展

## 代码说明
=======
代码说明：

| 文件        | 说明                      |
| --------- | ----------------------- |
| tools.php | redis连接代码，websocket广播代码 |
| init.php  | 清理redis相关数据集的代码         |
| ws.php    | 创建websocket服务器          |

连接编号存储在redis集合中，用户名以及用户数以键值形式存储在redis中。

## 使用说明

```shell
nohup /usr/bin/php /var/www/ws.php >> /dev/null 2>&1 &
```

## 数据格式

| 格式说明        | 接收对象   | json                                     |
| ----------- | ------ | ---------------------------------------- |
| 用户名检查       | 请求用户   | `{from:sys, type:nameCheck, data:[failsuccess]}` |
| 初始化用户列表     | 请求用户   | `{from:sys, type:initUser, data:[用户名数组]}` |
| 用户登录提示      | 所有用户   | `{from:sys, type:login, data:登录用户名}`     |
| 用户退出提示      | 除了请求用户 | `{from:sys, type:logout, data:退出用户名}`    |
| 转发public消息  | 除了请求用户 | `{from:请求用户, type:public, data:消息内容}`    |
| 转发private消息 | 指定用户   | `{from:请求用户, type:private, data:消息内容"}`  |



# 前台部分

## 浏览器要求

支持websocket对象

## 使用说明

打开index.html页面即可

# 数据格式

| 格式说明        | json                                  |
| ----------- | ------------------------------------- |
| 用户登录        | `{type:login, to:sys, data:你的名字}`     |
| 发送public消息  | `{type:public, to:all, data:消息内容}`    |
| 发送private消息 | `{type:private, to:消息接收人, data:消息内容}` |