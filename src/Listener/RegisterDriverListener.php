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
namespace Chllen\HyperfServiceMicroEtcd\Listener;



use Chllen\HyperfServiceMicro\DriverManager;
use Chllen\HyperfServiceMicroEtcd\EtcdDriver;

class RegisterDriverListener implements ListenerInterface
{
    /**
     * @var DriverManager
     */
    protected $driverManager;

    public function __construct(DriverManager $manager)
    {
        $this->driverManager = $manager;
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event)
    {
        $this->driverManager->register('etcd', make(EtcdDriver::class));
    }
}
