# guzzle-proxy-handler

This library was created to simplify working with proxy through 
**guzzle-http** library

`composer require akim-bow/guzzle-proxy-handler`

## Examples

#### Example 1

```php
// You can use this function to load proxies from config or create proxy array manually
$proxyObjects = \ProxyHandler\ProxyManager::getProxyObjects('path to config');

$sps = new SequenceProxyStrategy($proxyObjects);

$client = \ProxyHandler\ProxyManager::createProxyClient($sps, [
    'timeout' => 5,
]);

$client->get('https://google.com');
$client->get('https://google.com');
$client->get('https://google.com');

echo "Every request was send with configured proxy";
```

## Strategies

### Sequence Strategy

This default strategy loops through your proxies and use them consequently.

### Weight Strategy

This strategy can be used with a weight class for calculating weight for your request.
Cannot be used in async requests.
