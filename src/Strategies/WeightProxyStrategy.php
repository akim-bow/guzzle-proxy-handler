<?php

namespace ProxyHandler\Strategies;

use ProxyHandler\ProxyData;
use ProxyHandler\ProxyStrategy;
use Psr\Http\Message\ResponseInterface;

class WeightProxyStrategy implements ProxyStrategy {

    public function getNextProxy(): ProxyData {
        // TODO: Implement getNextProxy() method.
    }

    public function afterResponse(ResponseInterface $response, ProxyData $proxy): void {
        // TODO: Implement afterResponse() method.
    }
}