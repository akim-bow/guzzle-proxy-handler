<?php

namespace ProxyHandler;

use Psr\Http\Message\ResponseInterface;

interface ProxyStrategy {
    public function getNextProxy(): string;
    public function afterResponse(ResponseInterface $response, string $proxy): void;
}