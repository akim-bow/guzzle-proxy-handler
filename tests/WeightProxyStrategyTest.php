<?php

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProxyHandler\ProxyData;
use ProxyHandler\Strategies\WeightStrategy\WeightCalculator;
use ProxyHandler\Strategies\WeightStrategy\WeightProxyStrategy;

class WeightProxyStrategyTest extends TestCase {
    public function tearDown(): void {
        Carbon::setTestNow();
    }

    public function testSyncRequests() {
        $weights = [10, 20, 50, 5, 10, 30, 60];

        $dates = [
            Carbon::now()->addSeconds(30),
            Carbon::now()->addSeconds(65),
            Carbon::now()->addSeconds(70),
            Carbon::now()->addSeconds(80),
            Carbon::now()->addSeconds(90),
            Carbon::now()->addSeconds(95),
            Carbon::now()->addSeconds(100),
        ];

        $dateIterator = new ArrayIterator($dates);

        $wps = new WeightProxyStrategy($this->getProxyObjects(), $this->getMockedWeightCalculator($weights));

        $result = array_map(function() use ($wps, $dateIterator) {
            $proxy = $wps->getNextProxy();

            Carbon::setTestNow($dateIterator->current());

            $dateIterator->next();

            $wps->afterResponse(new Response([200, 301, 400][random_int(0, 2)]), $proxy);
            return $proxy;
        }, $weights);

        $this->assertEquals(['test1', 'test2', 'test1', 'test3', 'test3', 'test3', 'test2'], $result);
    }

    public function testAsyncRequests() {
        $this->expectException(Exception::class);

        $weights = [
            [10, 20, 50, 5, 10, 30, 60],
            [45, 10, 80, 70, 25, 15, 30],
            [45, 5, 60, 75, 10, 40, 55],
        ];

        $pause = 40;

        $mwc = $this->createMock(WeightCalculator::class);
        $resultArray = [];

        $mwc->method('getWeight')->willReturnOnConsecutiveCalls(...array_merge(...$weights));

        $wps = new WeightProxyStrategy($this->getProxyObjects(), $mwc);

        $result = array_map(function($weight) use ($wps, $pause, $mwc) {
            $proxies = array_map(fn() => $wps->getNextProxy(), range(0, count($weight) - 1));

            Carbon::setTestNow(Carbon::now()->addSeconds($pause));

            $lineProxyArr = [];

            foreach ($proxies as $proxy) {
                $lineProxyArr[] = $proxy;
                $wps->afterResponse(new Response([200, 301, 400][random_int(0, 2)]), $proxy);
            }

            return $lineProxyArr;
        }, $weights);

//        $this->assertEquals([
//            ['test1', 'test2', 'test3', 'test1', 'test2', 'test3', 'test1'],
//            ['test1', 'test2', 'test1', 'test3', 'test3', 'test3', 'test2'],
//            ['test1', 'test2', 'test1', 'test3', 'test3', 'test3', 'test2'],
//        ], $result);
    }

    private function getMockedWeightCalculator(array $mockedWeights): WeightCalculator {
        $weightCalculator = $this->createMock(WeightCalculator::class);
        $weightCalculator->method('getWeight')->willReturnOnConsecutiveCalls(...$mockedWeights);
        return $weightCalculator;
    }

    private function getProxyObjects(): array {
        return [
            new ProxyData('test1', 'russia'),
            new ProxyData('test2', 'russia'),
            new ProxyData('test3', 'russia'),
        ];
    }
}