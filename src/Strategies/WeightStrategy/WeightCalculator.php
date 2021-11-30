<?php

namespace ProxyHandler\Strategies\WeightStrategy;

use GuzzleHttp\Psr7\Response;

interface WeightCalculator {
    public function getWeight(Response $response): int;
}