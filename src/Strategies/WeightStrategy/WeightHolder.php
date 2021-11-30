<?php

namespace ProxyHandler\Strategies\WeightStrategy;

use Carbon\Carbon;

class WeightHolder {
    private const SECONDS_TO_UPDATE = 60;

    private int $weight = 0;
    private Carbon $updateDate;

    public function __construct() {
        $this->updateDate = Carbon::now();
    }

    public function getWeight(): int {
        return $this->weight;
    }

    public function updateWeight(int $weight) {
        if ($weight < 0) {
            return;
        }

        if ($this->updateDate < Carbon::now()->subSeconds(self::SECONDS_TO_UPDATE)) {
            $this->updateDate = Carbon::now();
            $this->weight = 0;
        }

        $this->weight += $weight;
    }
}