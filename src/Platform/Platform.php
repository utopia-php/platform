<?php

namespace Utopia\Platform;

use Exception;
use Utopia\CLI\Adapters\Generic;
use Utopia\CLI\CLI;
use Utopia\Http\Http;
use Utopia\Http\Route;
use Utopia\Queue\Adapter\Swoole\Server;
use Utopia\Queue\Worker;

abstract class Platform
{
    protected array $services = [
        'all' => [],
        Service::TYPE_CLI => [],
        Service::TYPE_HTTP => [],
        Service::TYPE_GRAPHQL => [],
        Service::TYPE_WORKER => [],
    ];

    protected CLI $cli;

    protected Worker $worker;

    /**
     * Initialize Application
     *
     * @return void
     */
    public function init(string $type, array $params = []): void
    {
        switch ($type) {
            case Service::TYPE_HTTP:
                $this->initHttp();
                break;
            case Service::TYPE_CLI:
                $this->initCLI($params);
                break;
            case Service::TYPE_GRAPHQL:
                $this->initGraphQL();
                break;
            case Service::TYPE_WORKER:
                $this->initWorker($params);
                break;
            default:
                throw new Exception('Please provide which type of initialization you want to carry out.');
        }
    }

    /**
     * Init HTTP service
     *
     * @param  Service  $service
     * @return void
     */
    protected function initHttp(): void
    {
        foreach ($this->services[Service::TYPE_HTTP] as $service) {
            foreach ($service->getActions() as $action) {
                /** @var Action $action */
                switch ($action->getType()) {
                    case Action::TYPE_INIT:
                        $hook = Http::init();
                        break;
                    case Action::TYPE_ERROR:
                        $hook = Http::error();
                        break;
                    case Action::TYPE_OPTIONS:
                        $hook = Http::options();
                        break;
                    case Action::TYPE_SHUTDOWN:
                        $hook = Http::shutdown();
                        break;
                    case Action::TYPE_DEFAULT:
                    default:
                        $hook = Http::addRoute($action->getHttpMethod(), $action->getHttpPath());
                        break;
                }

                $hook
                    ->groups($action->getGroups())
                    ->desc($action->getDesc() ?? '');

                if ($hook instanceof Route) {
                    if (! empty($action->getHttpAliasPath())) {
                        $hook->alias($action->getHttpAliasPath(), $action->getHttpAliasParams());
                    }
                }

                foreach ($action->getOptions() as $key => $option) {
                    switch ($option['type']) {
                        case 'param':
                            $key = substr($key, stripos($key, ':') + 1);
                            $hook->param($key, $option['default'], $option['validator'], $option['description'], $option['optional'], $option['injections']);
                            break;
                        case 'injection':
                            $hook->inject($option['name']);
                            break;
                    }
                }

                foreach ($action->getLabels() as $key => $label) {
                    $hook->label($key, $label);
                }

                $hook->action($action->getCallback());
            }
        }
    }

    /**
     * Init CLI Services
     *
     * @return void
     */
    protected function initCLI($params = []): void
    {
        $adapter = $params['adapter'] ?? new Generic();

        $this->cli ??= new CLI($adapter);
        foreach ($this->services[Service::TYPE_CLI] as $service) {
            foreach ($service->getActions() as $key => $action) {
                switch ($action->getType()) {
                    case Action::TYPE_INIT:
                        $hook = $this->cli->init();
                        break;
                    case Action::TYPE_ERROR:
                        $hook = $this->cli->error();
                        break;
                    case Action::TYPE_SHUTDOWN:
                        $hook = $this->cli->shutdown();
                        break;
                    case Action::TYPE_DEFAULT:
                    default:
                        $hook = $this->cli->task($key);
                        break;
                }
                $hook
                    ->groups($action->getGroups())
                    ->desc($action->getDesc() ?? '');

                foreach ($action->getOptions() as $key => $option) {
                    switch ($option['type']) {
                        case 'param':
                            $key = substr($key, stripos($key, ':') + 1);
                            $hook->param($key, $option['default'], $option['validator'], $option['description'], $option['optional'], $option['injections']);
                            break;
                        case 'injection':
                            $hook->inject($option['name']);
                            break;
                    }
                }

                foreach ($action->getLabels() as $key => $label) {
                    $hook->label($key, $label);
                }

                $hook->action($action->getCallback());
            }
        }
    }

    /**
     * Init worker Services
     *
     * @param  array  $params
     * @return void
     */
    protected function initWorker(array $params): void
    {
        $connection = $params['connection'] ?? null;
        $workersNum = $params['workersNum'] ?? 0;
        $workerName = $params['workerName'] ?? null;
        $queueName = $params['queueName'] ?? 'v1-'.$workerName;
        $adapter = new Server($connection, $workersNum, $queueName);
        $this->worker ??= new Worker($adapter);
        foreach ($this->services[Service::TYPE_WORKER] as $service) {
            foreach ($service->getActions() as $key => $action) {
                if (! str_contains(strtolower($key), $workerName)) {
                    continue;
                }

                switch ($action->getType()) {
                    case Action::TYPE_INIT:
                        $hook = $this->worker->init();
                        break;
                    case Action::TYPE_ERROR:
                        $hook = $this->worker->error();
                        break;
                    case Action::TYPE_SHUTDOWN:
                        $hook = $this->worker->shutdown();
                        break;
                    case Action::TYPE_DEFAULT:
                    default:
                        $hook = $this->worker->job();
                        break;
                }
                $hook
                    ->groups($action->getGroups())
                    ->desc($action->getDesc() ?? '');

                foreach ($action->getOptions() as $key => $option) {
                    switch ($option['type']) {
                        case 'param':
                            $key = substr($key, stripos($key, ':') + 1);
                            $hook->param($key, $option['default'], $option['validator'], $option['description'], $option['optional'], $option['injections']);
                            break;
                        case 'injection':
                            $hook->inject($option['name']);
                            break;
                    }
                }

                foreach ($action->getLabels() as $key => $label) {
                    $hook->label($key, $label);
                }

                $hook->action($action->getCallback());
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
     * @param  string  $key
     * @param  Service  $service
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
     * @param  string  $key
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
     * @param  string  $key
     * @return Service|null
     */
    public function getService(string $key): ?Service
    {
        if (empty($this->services['all'][$key])) {
            throw new Exception('Service '.$key.' not found');
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

    /**
     * Get the value of worker
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * Set the value of worker
     */
    public function setWorker(Worker $worker): self
    {
        $this->worker = $worker;

        return $this;
    }
}
