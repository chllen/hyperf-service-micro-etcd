<?php

declare(strict_types=1);

namespace Chllen\HyperfServiceMicroEtcd\Etcd;

use Chllen\HyperfServiceMicroEtcd\Etcd\V3\Watcher;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Etcd\Exception\ClientNotFindException;
use Hyperf\Guzzle\HandlerStackFactory;
use Psr\Container\ContainerInterface;

class WatcherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $uri = $config->get('etcd.uri', 'http://127.0.0.1:2379');
        $version = $config->get('etcd.version', 'v3beta');
        $options = $config->get('etcd.options', []);

        return $this->make($uri, $version, $options);
    }

    protected function make(string $uri, string $version, array $options)
    {
        $params = [
            'uri' => $uri,
            'version' => $version,
            'options' => $options,
        ];

        switch ($version) {
            case 'v3':
            case 'v3alpha':
            case 'v3beta':
                return make(Watcher::class, $params);
        }

        throw new ClientNotFindException(sprintf("Watch of {$version} is not find."));
    }
}
