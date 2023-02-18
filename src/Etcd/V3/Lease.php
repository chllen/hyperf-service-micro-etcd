<?php


namespace Chllen\HyperfServiceMicroEtcd\Etcd\V3;

use Hyperf\Etcd\Client;
use Chllen\HyperfServiceMicroEtcd\Etcd\LeaseInterface;
use Hyperf\Etcd\V3\EtcdClient;

class Lease extends Client implements LeaseInterface
{
    public function grant($ttl,$id = 0)
    {
        return $this->client()->grant($ttl, $id);
    }

    public function keepAlive($id = 0)
    {
        return $this->client()->keepAlive($id);
    }

    protected function client(): EtcdClient
    {
        $options = array_replace([
            'base_uri' => $this->baseUri,
            'handler' => $this->getDefaultHandler(),
        ], $this->options);

        $client = make(\GuzzleHttp\Client::class, [
            'config' => $options,
        ]);

        return make(EtcdClient::class, [
            'client' => $client,
        ]);
    }
}