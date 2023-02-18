<?php

declare(strict_types=1);

namespace Chllen\HyperfServiceMicroEtcd;

use Chllen\HyperfServiceMicroEtcd\Listener\RegisterDriverListener;
use Chllen\HyperfServiceMicroEtcd\Etcd\LeaseInterface;
use Chllen\HyperfServiceMicroEtcd\Etcd\LeaseFactory;
use Chllen\HyperfServiceMicroEtcd\Etcd\WatcherInterface;
use Chllen\HyperfServiceMicroEtcd\Etcd\WatcherFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                LeaseInterface::class=>LeaseFactory::class,
                WatcherInterface::class=>WatcherFactory::class,
            ],
            'listeners' => [
                RegisterDriverListener::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for etcd.',
                    'source' => __DIR__ . '/../publish/etcd.php',
                    'destination' => BASE_PATH . '/config/autoload/etcd.php',
                ],
            ],
        ];
    }
}
