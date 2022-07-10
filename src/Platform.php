<?php

namespace Utopia\Platform;

use Utopia\App;

abstract class Platform {
    protected array $services = [];

    public function init():void
    {
        return;
    }

    public function addService(string $key, Service $service): Platform
    {
        $this->services[$key] = $service;
        return $this;
    }

    public function removeService(string $key): Platform
    {
        unset($this->services[$key]);
        return $this;
    }

    public function getService(string $key): ?Service
    {
        return $this->services[$key] ?? null;
    }

    public function getServices(): array
    {
        return $this->services;
    }
}
