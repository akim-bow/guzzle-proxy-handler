<?php

namespace ProxyHandler;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ProxyManager {
    /** @return array<ProxyData> */
    public static function getProxyObjects(string $pathToConfig): array {
        if (!is_file($pathToConfig)) {
            throw new \Exception('Cannot find proxy config');
        }

        $proxies = json_decode(file_get_contents($pathToConfig), true)['proxies'];

        return array_map(function(array $proxy) {
            return new ProxyData($proxy['url'], $proxy['country']);
        }, $proxies);
    }

    public static function createProxyClient(
        ProxyStrategy $proxyStrategy,
        array $guzzleParams = [],
    ): Client {
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push(self::getMiddleware($proxyStrategy));
        return new Client(array_merge($guzzleParams, ['handler' => $stack]));
    }

    private static function getMiddleware(ProxyStrategy $proxyStrategy): callable {
        return function(callable $handler) use ($proxyStrategy) {
            return function(\Psr\Http\Message\RequestInterface $request, array &$options) use ($handler, $proxyStrategy) {
                $proxy = $proxyStrategy->getNextProxy();
                $options['proxy'] = $proxy;

                return $handler($request, $options)->then(function(ResponseInterface $response) use ($proxy, $proxyStrategy) {
                    $proxyStrategy->afterResponse($response, $proxy);
                    return $response;
                });
            };
        };
    }
}