<?php

namespace Utopia\Platform;

use Exception;

abstract class Module
{
    /**
     * @var array<string, array<string, Service>>
     */
    protected array $services = [
        'all' => [],
        Service::TYPE_TASK => [],
        Service::TYPE_HTTP => [],
        Service::TYPE_GRAPHQL => [],
        Service::TYPE_WORKER => [],
    ];

    /**
     * Add Service
     *
     * @param  string  $key
     * @param  Service  $service
     * @return self
     */
    public function addService(string $key, Service $service): self
    {
        $this->services['all'][$key] = $service;
        $this->services[$service->getType()][$key] = $service;

        return $this;
    }

    /**
     * Remove Service
     *
     * @param  string  $key
     * @return self
     */
    public function removeService(string $key): self
    {
        $service = $this->services['all'][$key] ?? null;
        if (empty($service)) {
            return $this;
        }
        $type = $service->getType();
        unset($this->services['all'][$key]);
        unset($this->services[$type][$key]);

        return $this;
    }

    /**
     * Get Service
     *
     * @param string $key
     * @return Service|null
     * @throws Exception
     */
    public function getService(string $key): ?Service
    {
        $service = $this->services['all'][$key] ?? null;
        if (empty($service)) {
            throw new Exception('Service '.$key.' not found');
        }

        return $service;
    }

    /**
     * Get Services
     *
     * @return array<string, Service>
     */
    public function getServices(): array
    {
        return $this->services['all'];
    }

    /**
     * Get services by type
     *
     * @param  string  $type
     * @return array<string, Service>
     */
    public function getServicesByType(string $type): array
    {
        return $this->services[$type] ?? [];
    }
}
