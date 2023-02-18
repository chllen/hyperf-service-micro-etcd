<?php

declare(strict_types=1);

namespace Chllen\HyperfServiceMicroEtcd\Etcd;

use Chllen\HyperfServiceMicroEtcd\Etcd\V3\Lease;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Etcd\Exception\ClientNotFindException;
use Hyperf\Guzzle\HandlerStackFactory;
use Psr\Container\ContainerInterface;

class LeaseFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $uri = $config->get('etcd.uri', 'http://127.0.0.1:2379');
        $version = $config->get('etcd.version', 'v3beta');
        $options = $config->get('etcd.options', []);
        $factory = $container->get(HandlerStackFactory::class);

        return $this->make($uri, $version, $options, $factory);
    }

    protected function make(string $uri, string $version, array $options, HandlerStackFactory $factory)
    {
        $params = [
            'uri' => $uri,
            'version' => $version,
            'options' => $options,
            'factory' => $factory,
        ];

        switch ($version) {
            case 'v3':
            case 'v3alpha':
            case 'v3beta':
                return make(Lease::class, $params);
        }

        throw new ClientNotFindException(sprintf("lease of {$version} is not find."));
    }
}
