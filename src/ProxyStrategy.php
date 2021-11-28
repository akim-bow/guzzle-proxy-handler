<?php

namespace ProxyHandler;

use Psr\Http\Message\ResponseInterface;

interface ProxyStrategy {
    public function getNextProxy(): ProxyData;
    public function afterResponse(ResponseInterface $response, ProxyData $proxy): void;
}