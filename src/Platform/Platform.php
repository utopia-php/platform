<?php

namespace Utopia\Platform;

use Utopia\App;
use Utopia\CLI\CLI;
use Exception;

abstract class Platform
{
    protected array $services = [
        'all' => [],
        Service::TYPE_CLI => [],
        Service::TYPE_HTTP => [],
        Service::TYPE_GRAPHQL => []
    ];

    protected CLI $cli;

    /**
     * Initialize Application
     *
     * @return void
     */
    public function init(string $type = null): void
    {
        switch ($type) {
            case Service::TYPE_HTTP:
                $this->initHttp();
                break;
            case Service::TYPE_CLI:
                $this->initCLI();
                break;
            case Service::TYPE_GRAPHQL:
                $this->initGraphQL();
                break;
            case 'all':
            default:
                $this->initHttp();
                $this->initCli();
                $this->initGraphQL();
                break;
        }
    }

    /**
     * Init HTTP service
     *
     * @param Service $service
     * @return void
     */
    protected function initHttp(): void
    {
        foreach ($this->services[Service::TYPE_HTTP] as $service) {
            foreach ($service->getActions() as $action) {
                /** @var Action $action */
                $route = App::addRoute($action->getHttpMethod(), $action->getHttpPath());
                $route
                    ->groups($action->getGroups())
                    ->desc($action->getDesc() ?? '');

                if (!empty($action->getHttpAliasPath())) {
                    $route->alias($action->getHttpAliasPath(), $action->getHttpAliasParams());
                }

                foreach ($action->getOptions() as $key => $option) {
                    switch ($option['type']) {
                        case 'param':
                            $key = substr($key, stripos($key, ':') + 1);
                            $route->param($key, $option['default'], $option['validator'], $option['description'], $option['optional'], $option['injections']);
                            break;
                        case 'injection':
                            $route->inject($option['name']);
                            break;
                    }
                }

                foreach ($action->getLabels() as $key => $label) {
                    $route->label($key, $label);
                }

                $route->action($action->getCallback());
            }
        }
    }

    /**
     * Init CLI Services
     *
     * @return void
     */
    protected function initCLI(): void
    {
        $this->cli ??= new CLI();
        foreach ($this->services[Service::TYPE_CLI] as $service) {
            foreach ($service->getActions() as $key => $action) {
                $task = $this->cli->task($key);
                $task
                    ->desc($action->getDesc() ?? '')
                    ->action($action->getCallback());

                foreach ($action->getParams() as $key => $param) {
                    $task->param($key, $param['default'], $param['validator'], $param['description'], $param['optional']);
                }

                foreach ($action->getLabels() as $key => $label) {
                    $task->label($key, $label);
                }
            }
        }
    }

    /**
     * Initialize GraphQL Services
     *
     * @return void
     */
    protected function initGraphQL(): void
    {
    }

    /**
     * Add Service
     *
     * @param string $key
     * @param Service $service
     * @return Platform
     */
    public function addService(string $key, Service $service): Platform
    {
        $this->services['all'][$key] = $service;
        $this->services[$service->getType()][$key] = $service;
        return $this;
    }

    /**
     * Remove Service
     *
     * @param string $key
     * @return Platform
     */
    public function removeService(string $key): Platform
    {
        unset($this->services[$key]);
        return $this;
    }


    /**
     * Get Service
     *
     * @param string $key
     * @return Service
     */
    public function getService(string $key): Service
    {
        if (empty($this->services['all'][$key])) {
            throw new Exception('Service ' . $key . ' not found');
        }
        return $this->services['all'][$key] ?? null;
    }


    /**
     * Get Services
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services['all'];
    }

    /**
     * Get the value of cli
     */
    public function getCli(): CLI
    {
        return $this->cli;
    }

    /**
     * Set the value of cli
     */
    public function setCli(CLI $cli): self
    {
        $this->cli = $cli;

        return $this;
    }
}
