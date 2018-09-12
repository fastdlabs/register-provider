# sentinel
FastD sentinel

## 说明
仅支持php-cli

## 配置FastD sentinel

- 添加配置

在config/config.php添加配置registry

```php
<?php

return [
    'registry' => qconf_get_values('/zookeeper path', null, null, [
        'host' => 'tcp://registry host',
        //连接重试间隔，时间单位ms
        'retry_interval' => 1000,
    ]),
];
```

host 为发现服务器接受数据的地址，由 fastd-register 启动 swoole server 时决定，可以根据启动的ip跟端口进行调整。

- 注册

在config/process.php添加配置

 ```php
 <?php
 
 return [
     'sentinel' => [
         'process' => \FastD\RegistryProvider\Process\RegisterProcess::class,
         'options' => [
 
         ],
     ],
 ];
 ```

在FPM的模式下，可以通过: `php bin/process sentinel start` 命令进行启动
 
如果在swoole模式下，则需要在 `server.php` 文件中添加进程: 


```php
<?php

return [
    // some code
    'processes' => [
        \FastD\RegistryProvider\Process\RegisterProcess::class,
    ],
    // some code
];
```