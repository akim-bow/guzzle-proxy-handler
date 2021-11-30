<?php

namespace ProxyHandler;

class ProxyData {
    public string $url;
    public string $country;

    /**
     * @param string $url
     * @param string $country
     */
    public function __construct(string $url, string $country) {
        $this->url = $url;
        $this->country = $country;
    }
}