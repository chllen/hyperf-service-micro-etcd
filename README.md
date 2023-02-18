# hyperf-service-micro-etcd
#介绍
该组件基于hyperf2.0框架，实现了以etcd为服务中心的grpc服务注册,支持于go-micro框架数据交互。

安装组件:
```
composer require hyperf/grpc-server
```

#配置：
组件由 config/autoload/services.php 配置文件来驱动，配置文件如下：
```bash
return [
    //省略其他配置
    'go_micro_consumers' =>[
        [
            'service_name'=>'userService',
            'path_prefix'=>'/micro/registry',
            'registry' => [
                'protocol' => 'etcd',
                'address' => '127.0.0.1:2379',
            ],
            'protocol' => 'grpc',
            'load_balancer' => 'random',
            'nodes' => [
                ['host' => '127.0.0.1', 'port' => 9504],
            ]
        ],
    ],
]
```

gRPC server 服务器配置
server.php 文件
```bash
'servers' => [
    ....
    [
        'name' => 'grpc',
        'type' => Server::SERVER_HTTP,
        'host' => '0.0.0.0',
        'port' => 9503,
        'sock_type' => SWOOLE_SOCK_TCP,
        'callbacks' => [
            Event::ON_REQUEST => [\Hyperf\GrpcServer\Server::class, 'onRequest'],
        ],
    ],
],
```

#示例：
gRPC server 路由配置：
routes.php 文件
```bash
Router::addServer('grpc', function () {
    Router::addGroup('/orderService', function () {
        Router::post('/create', 'App\Controller\OrderController@create');
    });
});
```

新建OrderController.php 文件中的 createOrder 方法:
```bash
/**
 * @RpcService(name="orderService", version='latest',protocol="grpc",publishTo="etcd")
 */
public function create(Order $order) 
{
    $message = new OrderReply();
    $message->setMessage("Order created success");
    return $message;
}
```

.proto 文件中的定义和 gRPC server 路由的对应关系: /{package}.{service}/{rpc}