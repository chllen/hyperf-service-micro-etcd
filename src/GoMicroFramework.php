<?php

namespace Chllen\HyperfServiceMicroEtcd;

use Chllen\HyperfGrpcClient\FrameworkInterface;

class GoMicroFramework implements FrameworkInterface
{
    public function parseValue(string $body): ?array
    {
        $value = json_decode((string)$body, true);
        if (isset($value['nodes']) && isset($value['name'])) {
            $nodes = [];
            foreach ($value['nodes'] as $node) {
                if (isset($node['address']) && isset($node['id'])) {
                    $id = $node['id'];
                    list($host, $port) = explode(':', $node['address']);
                    $nodes[$id][] = ['host' => $host, 'port' => $port];
                }
            }
            return [
                'name' => $value['name'] ?? '',
                'id' => $id ?? uniqid(),
                'nodes' => $nodes,
            ];
        }
        return null;
    }

    public function parseKey(string $key): ?array
    {
        $data = explode('/',$key);
        if(count($data) < 2) return null;
        $data = array_slice($data,-2,2);
        return [
            'name' => $data[0],
            'id' => $data[1],
        ];
    }
}