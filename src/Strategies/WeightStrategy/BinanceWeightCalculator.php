<?php

namespace ProxyHandler\Strategies\WeightStrategy;

use GuzzleHttp\Psr7\Response;

class BinanceWeightCalculator implements WeightCalculator {
    private const MINUTE_LIMIT = 2400;
    private const MINUTE_LIMIT_HEADER = 'X-MBX-USED-WEIGHT-1M';

    public function getWeight(Response $response): int {
        $header = $response->getHeader(self::MINUTE_LIMIT_HEADER);

        return count($header) == 0 ? 0 : $header[0];
    }
}