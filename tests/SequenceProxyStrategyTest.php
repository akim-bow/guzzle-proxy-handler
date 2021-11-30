<?php

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProxyHandler\ProxyData;
use ProxyHandler\Strategies\SequenceStrategy\SequenceProxyStrategy;

class SequenceProxyStrategyTest extends TestCase {
    public function testSyncRequests() {
        $sps = new SequenceProxyStrategy($this->getProxyObjects());
        $result = array_map(function() use ($sps) {
            $proxy = $sps->getNextProxy();
            $sps->afterResponse(new Response([200, 301, 400][random_int(0, 2)]), $proxy);
            return $proxy;
        }, range(0, 9));

        $this->assertEquals(['test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1'], $result);
    }

    public function testAsyncRequests() {
        $sps = new SequenceProxyStrategy($this->getProxyObjects());
        $proxies = array_map(function() use ($sps) {
            return $sps->getNextProxy();
        }, range(0, 9));

        for($i = 0; $i < 10; $i++) {
            $sps->afterResponse(new Response([200, 301, 400][random_int(0, 2)]), $proxies[$i]);
        }

        $this->assertEquals(['test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1'], $proxies);
    }

    private function getProxyObjects(): array {
        return [
            new ProxyData('test1', 'russia'),
            new ProxyData('test2', 'russia'),
            new ProxyData('test3', 'russia'),
        ];
    }
}