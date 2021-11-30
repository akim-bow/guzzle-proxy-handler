<?php

namespace ProxyHandler\Strategies\SequenceStrategy;

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

        $this->proxies = array_map(fn(ProxyData $proxyData) => $proxyData->url, $proxies);
    }

    public function getNextProxy(): string {
        $proxy = $this->proxies[$this->index++];

        if ($this->index >= count($this->proxies)) {
            $this->index = 0;
        }

        return $proxy;
    }

    public function afterResponse(ResponseInterface $response, string $proxy): void {
        // TODO: Implement updateFromResponse() method.
    }
}