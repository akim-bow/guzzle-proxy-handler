<?php

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProxyHandler\ProxyData;

class SequenceProxyStrategyTest extends TestCase {
    public function testSyncRequests() {
        $sps = new \ProxyHandler\Strategies\SequenceProxyStrategy($this->getProxyObjects());
        $result = array_map(function() use ($sps) {
            $proxy = $sps->getNextProxy();
            $sps->afterResponse(new Response([200, 301, 400][random_int(0, 2)]), $proxy);
            return $proxy->url;
        }, range(0, 9));

        $this->assertEquals(['test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1'], $result);
    }

    public function testAsyncRequests() {
        $sps = new \ProxyHandler\Strategies\SequenceProxyStrategy($this->getProxyObjects());
        $proxyObjects = array_map(function() use ($sps) {
            return $sps->getNextProxy();
        }, range(0, 9));

        for($i = 0; $i < 10; $i++) {
            $sps->afterResponse(new Response([200, 301, 400][random_int(0, 2)]), $proxyObjects[$i]);
        }

        $urls = array_map(fn($proxyObject) => $proxyObject->url, $proxyObjects);

        $this->assertEquals(['test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1'], $urls);
    }

    private function getProxyObjects(): array {
        return [
            new ProxyData('test1', 'russia'),
            new ProxyData('test2', 'russia'),
            new ProxyData('test3', 'russia'),
        ];
    }
}