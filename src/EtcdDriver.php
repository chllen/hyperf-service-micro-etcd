<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Chllen\HyperfServiceMicroEtcd;

use Chllen\HyperfServiceMicroEtcd\Etcd\LeaseInterface;
use Chllen\HyperfServiceMicroEtcd\Etcd\V3\Lease;
use Chllen\HyperfServiceMicroEtcd\Etcd\WatcherInterface;
use Chllen\HyperfServiceMicroEtcd\Etcd\V3\Watcher;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Etcd\KVInterface;
use Hyperf\Etcd\V3\KV;
use Hyperf\ServiceGovernance\DriverInterface;
use Hyperf\ServiceGovernanceConsul\ConsulAgent;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use Etcdserverpb\WatchCreateRequest\FilterType;

class EtcdDriver implements DriverInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $registeredServices = [];

    protected $health;

    protected $prefix = "/micro/registry";

    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(StdoutLoggerInterface::class);
        $this->config = $container->get(ConfigInterface::class);
    }

    public function getNodes(string $uri, string $name, array $metadata): array
    {
        $etcdResponse = make(KVInterface::class, [
            'uri' => $uri,
            'version' => 'v3beta',
        ])->fetchByPrefix(join('/', [$metadata['path_prefix'] ?? '', $name]));

        $nodes = [];
        if (isset($etcdResponse['kvs'])) {
            $services = $etcdResponse['kvs'];
            foreach ($services as $service) {
                if (isset($service['value'])) {
                    $value = json_decode($service['value']);
                    if (isset($value->nodes)) {
                        foreach ($value->nodes as $node) {
                            if (isset($node->address) && isset($node->id)) {
                                $id = $node->id;
                                list($host, $port) = explode(':', $node->address);
                                $nodes[$id][] = ['host' => $host, 'port' => $port];
                            }
                        }
                    }
                }
            }
        }

        return $nodes;
    }

    public function register(string $name, string $host, int $port, array $metadata): void
    {
        \Hyperf\Utils\Coroutine::create(function () use ($name, $host, $port, $metadata) {
            $kv = $this->kvClient();
            $lease = $this->leaseClient();
            $curLeaseID = 0;
            while (true) {
                if ($curLeaseID == 0) {
                    $curLeaseID = data_get($lease->grant(5), 'ID', 0);
                    $key = $this->generateId($name);
                    $requestBody = [
                        "name" => $name,
                        "version" => $metadata['version'] ?? '',
                        "metadata" => null,
                        "endpoints" => [],
                        "nodes" => [
                            [
                                "id" => $key,
                                "address" => join(':', [$host, $port]),
                                "metadata" => [
                                    "broker" => "http",
                                    "protocol" => "grpc",
                                    "registry" => "etcd",
                                    "server" => "grpc",
                                    "transport" => "grpc"
                                ]
                            ]
                        ],
                    ];
                    $this->kvClient()->put($key, json_encode($requestBody), ['lease' => (int)$curLeaseID]);
                } else {
                    try {
                        $this->LeaseClient()->KeepAlive($curLeaseID);
                    } catch (\Exception $e) {
                        $curLeaseID = 0;
                        continue;
                    }
                    sleep(1);
                }
            }
        });
    }

    public function isRegistered(string $name, string $address, int $port, array $metadata): bool
    {
        $protocol = $metadata['protocol'];
        if (isset($this->registeredServices[$name][$protocol][$address][$port])) {
            return true;
        }
        return false;
    }

    protected function kvClient(): KVInterface
    {
        return $this->container->get(KVInterface::class);
    }

    protected function leaseClient(): LeaseInterface
    {
        return $this->container->get(LeaseInterface::class);
    }


    protected function getLastServiceId(string $name)
    {

    }

    protected function generateId(string $name)
    {
        $uuid = Uuid::uuid4();
        return sprintf("%s/%s/%s", $this->prefix, $name, $name . '-' . $uuid->toString());
    }
}
