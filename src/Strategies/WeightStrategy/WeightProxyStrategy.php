<?php

namespace ProxyHandler\Strategies\WeightStrategy;

use ProxyHandler\ProxyData;
use ProxyHandler\ProxyStrategy;
use Psr\Http\Message\ResponseInterface;

class WeightProxyStrategy implements ProxyStrategy {
    /** @var array<string, WeightHolder> $proxyWeights */
    private array $proxyWeights;
    private WeightCalculator $weightCalculator;
    private bool $waitForResp = false;

    public function __construct(array $proxies, WeightCalculator $weightCalculator) {
        if (count($proxies) === 0) {
            throw new \Exception('Initialized with empty array');
        }

        $this->weightCalculator = $weightCalculator;

        $urls = array_map(fn(ProxyData $proxyData) => $proxyData->url, $proxies);

        $this->proxyWeights = array_combine($urls, array_map(fn() => new WeightHolder(), $urls));
    }

    public function getNextProxy(): string {
        if ($this->waitForResp) {
            throw new \Exception('This strategy do not support async requests');
        }

        $this->waitForResp = true;
        $this->updateProxiesWeight();

        return $this->getLessWeightedProxyUrl();
    }

    public function afterResponse(ResponseInterface $response, string $proxy): void {
        $this->waitForResp = false;
        $weight = $this->weightCalculator->getWeight($response);
        $this->addProxyWeight($proxy, $weight);
    }

    private function addProxyWeight(string $proxy, int $weight) {
        $this->proxyWeights[$proxy]->updateWeight($weight);
    }

    private function updateProxiesWeight() {
        foreach ($this->proxyWeights as $proxyWeight) {
            $proxyWeight->updateWeight(0);
        }
    }

    private function getLessWeightedProxyUrl(): string {
        $maxProxyUrl = array_key_first($this->proxyWeights);
        $maxProxy = $this->proxyWeights[$maxProxyUrl];

        foreach ($this->proxyWeights as $url => $proxyWeight) {
            if ($maxProxy->getWeight() > $proxyWeight->getWeight()) {
                $maxProxyUrl = $url;
                $maxProxy = $proxyWeight;
            }
        }

        return $maxProxyUrl;
    }
}