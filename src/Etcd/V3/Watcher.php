<?php
namespace Chllen\HyperfServiceMicroEtcd\Etcd\V3;

use Chllen\HyperfServiceMicroEtcd\Etcd\WatcherInterface;
use Etcdserverpb\WatchCreateRequest;
use Etcdserverpb\WatchCreateRequest\FilterType;
use Etcdserverpb\WatchRequest;
use Hyperf\Guzzle\HandlerStackFactory;
use Swoole\Coroutine;

class Watcher implements WatcherInterface
{
    protected $baseUri;

    public function __construct(string $uri, string $version, array $options)
    {
        $this->options = $options;
        $this->baseUri = $uri;
    }

    public function watch(string $key,array $options=[])
    {
        $watchCall = $this->client()->Watch();
        $request = new WatchRequest();
        $createRequest = new WatchCreateRequest();
        $createRequest->setKey($key);
        if(!empty($options['range_end'])){
            $createRequest->setRangeEnd($options['range_end']);
        }
        $request->setCreateRequest($createRequest);
        $watchCall->push($request);
        return $watchCall;
    }

    public function watchPrefix(string $prefix)
    {
        $prefix = trim($prefix);
        if (! $prefix) {
            return [];
        }
        $lastIndex = strlen($prefix) - 1;
        $lastChar = $prefix[$lastIndex];
        $nextAsciiCode = ord($lastChar) + 1;
        $rangeEnd = $prefix;
        $rangeEnd[$lastIndex] = chr($nextAsciiCode);
        return $this->watch($prefix,['range_end'=>$rangeEnd]);
    }


    protected function client()
    {
        $watchClient = make(\Etcdserverpb\WatchClient::class,['hostname'=>$this->baseUri]);
        //协程结束时执行
        defer(function () use($watchClient){
            $watchClient->close();
        });
        return $watchClient;
    }
}


