<?php

namespace ProxyHandler\Strategies;

use ProxyHandler\ProxyData;
use ProxyHandler\ProxyStrategy;
use Psr\Http\Message\ResponseInterface;

class SequenceProxyStrategy implements ProxyStrategy {
    /** @var array<ProxyData> $proxies */
    private array $proxies;
    private int $index = 0;

    public function __construct(array $proxies) {
        if (count($proxies) === 0) {
            throw new \Exception('Initialized with empty array');
        }

        $this->proxies = $proxies;
    }

    public function getNextProxy(): ProxyData {
        $proxy = $this->proxies[$this->index++];

        if ($this->index >= count($this->proxies)) {
            $this->index = 0;
        }

        return $proxy;
    }

    public function afterResponse(ResponseInterface $response, ProxyData $proxy): void {
        // TODO: Implement updateFromResponse() method.
    }

    private function isProxyUsed(int $statusCode): bool {
        return $statusCode >= 200 && $statusCode < 300 || $statusCode >= 400 && $statusCode < 500;
    }
}