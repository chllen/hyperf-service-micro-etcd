<?php


namespace Chllen\HyperfServiceMicroEtcd\Etcd;


interface WatcherInterface
{
        public function watch(string $key,array $options);

        public function watchPrefix(string $prefix);
}