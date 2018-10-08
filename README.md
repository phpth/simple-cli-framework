# simple-cli-framework
简单的php-cli 执行框架

> 缘起 

 - 在前一段时间的工作中，遇到线上web服务器需要cli环境来处理大量数据和用户日志，日志数据都是txt或者log文件，足足有几个TB。需要把这些数据文件处理成预定的格式
 ，然后再写入阿里云oss当中。
 - 但是线上web环境的配置做了很多安全性的配置，比如禁用webshell函数，限制工作目录等等。处理大量的数据需要一些linux命令配合，并且需要程序中开启多进程处理方式。
 唯有cli模式下采用针对cli的特殊配置才能实现想要的效果，又不能改动web的配置，所以就有了写一个简单的cli程序。在程序入口里面指定cli配置。初步设想的实现方式是：
 在入口文件声明php可执行文件路径并且指定配置等参数。cli入口文件如下
```php
#!/usr/local/bin/php -c /usr/local/etc/cli.ini
<?php
  // php code 
    
```
 - 使用不同的配置是实现了，但是随着越来越多的数据处理逻辑，零散的php文件也变得杂乱，难以维护，所以就干脆写一个cli小框架
 
> 框架目录结构
```text
project  应用部署目录
├─app           应用目录(一级)
│  ├─index              可分模块(二级)
│  │  ├─pushData.php    实际处理逻辑控制器文件
│  │  └─ ...            更多控制器文件
│  ├─index.php          数据库配置文件
│  ├─test.php           应用行为扩展定义文件
│  └─ ...               更多控制器处理文件
├─bin                   框架核心文件
│  ├─mode               执行模式(当前只有cli)
│  │  ├─cli.php         cli模式的处理 (简单路由，命令参数简单处理成$_GET参数)
│  │  └─ mode.php       用于未来支持其他模式
│  ├─tool               附带的一些其他工具类
│  │  ├─ net.php
│  │  ├─ log.php
│  │  └─ ...
│  ├─app.php            框架核心运行逻辑，包括自动加载处理
│  ├─config.php         配置获取和设置类
│  └─process.php        内置的多进程管理类
├─build                 打包框架处理成phar可执行文件
│  └─build              简单的打包逻辑
├─common  
│  ├─common.php         一些公共处理函数(默认被加载)
│  └─config.php         配置文件(可在入口文件重新定义)
├─public                预留WEB 部署目录（对外访问目录）
│  └─index.php          应用入口文件
├─tmp                   应用的运行时目录（可写）
├─vendor                第三方类库目录（Composer）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─start                 命令行入口文件

```

> 使用说明
 
 - 框架默认在app下有一个index.php控制器文件，执行如下操作：
 ```sh
root@ae7fae07a94d:~# cd simple-cli-framework
root@ae7fae07a94d:~/simple-cli-framework# ./start

  hello tiny-cli framework~ 

root@ae7fae07a94d:~/simple-cli-framework#
 
```
 > start入口文件
 - 命令格式 php 入口文件(start) 控制器路由(index/index) [-pc param1,param2] [-pa param1,param2]
 - 参数pc: 代表向控制器构造方法传入参数
 - 参数pa: 代表向控制器逻辑处理方法传入参数
 
 > 额外的多进程管理类
 
 ```php
<?php
 namespace app;
 
 use bin\config;
 use bin\process;
 class index
 {
     public function testProcess()
     {
         $cmd_arr = [
             [
                'cmd'=>'./start index/run -pa run_process_1',
                'out'=>'./tmp/process1.log',     
            ],
            [
                'cmd'=>'./start index/run -pa run_process_2',
                'out'=>'./tmp/process2.log',     
            ],
        ];
         $process = new process($cmd_arr);
         $process->run();
         /*
          code。。。
         
           
           */
         $process->wait (1);//执行一次 , 如果传入0 则则会在进程结束后重启进程
     }
     
     public function run($param)
     {
         var_dump($param);
         sleep(10);
     }
 }
 
 ```
 

免责：当前框架并未针对web环境做安全处理措施，目前情况下请不要用于web环境

> 结束语

后期会增加一些比较有用的客户端，pdo重连客户端，redis安全队列等等
