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



use Chllen\HyperfGrpcClient\FrameworkManager;
use Chllen\HyperfServiceMicroEtcd\GoMicroFramework;
use Hyperf\ServiceGovernance\DriverManager;
use Chllen\HyperfServiceMicroEtcd\EtcdDriver;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;

class RegisterDriverListener implements ListenerInterface
{
    /**
     * @var DriverManager
     */
    protected $driverManager;

    /**
     * @var FrameworkManager
     */
    protected $frameworkManager;

    public function __construct(DriverManager $driver,FrameworkManager $framework)
    {
        $this->driverManager = $driver;
        $this->frameworkManager = $framework;
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event)
    {
        //注册go-micro框架解析
        $this->frameworkManager->register('go-micro', make(GoMicroFramework::class));
        $this->driverManager->register('etcd', make(EtcdDriver::class));
    }
}
