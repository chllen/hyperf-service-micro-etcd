<?php


namespace Chllen\HyperfServiceMicroEtcd\Etcd;


interface LeaseInterface
{
    public function grant($ttl,int $id);

    public function keepAlive(int $id);
}