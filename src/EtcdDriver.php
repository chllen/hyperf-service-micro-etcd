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
    
    }

    public function register(string $name, string $host, int $port, array $metadata): void
    {
 
    }

    public function isRegistered(string $name, string $address, int $port, array $metadata): bool
    {
        
    }

    protected function client(): AgentInterface
    {
       
    }

    protected function getLastServiceId(string $name)
    {
       
    }

    protected function generateId(string $name)
    {
        $exploded = explode('-', $name);
        $length = count($exploded);
        $end = -1;
        if ($length > 1 && is_numeric($exploded[$length - 1])) {
            $end = $exploded[$length - 1];
            unset($exploded[$length - 1]);
        }
        $end = intval($end);
        ++$end;
        $exploded[] = $end;
        return implode('-', $exploded);
    }

    protected function createConsulHealth(string $baseUri): HealthInterface
    {
        
    }
}
