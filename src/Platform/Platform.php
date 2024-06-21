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
    protected Module $core;

    /**
     * Modules
     *
     * @var array<Module>
     */
    protected array $modules = [];

    protected CLI $cli;

    protected Worker $worker;

    public function __construct(Module $module)
    {
        $this->core = $module;
        $this->modules[] = $module;
    }

    /**
     * Initialize Application
     *
     * @return void
     */
    public function init(string $type, array $params = []): void
    {
        foreach ($this->modules as $module) {
            $services = $module->getServicesByType($type);
            switch ($type) {
                case Service::TYPE_HTTP:
                    $this->initHttp($services);
                    break;
            case Service::TYPE_TASK:
                $adapter = $params['adapter'] ?? new Generic();
                $this->cli ??= new CLI($adapter);
                $this->initTasks($services);
                    break;
                case Service::TYPE_GRAPHQL:
                    $this->initGraphQL();
                    break;
                case Service::TYPE_WORKER:
                    $workerName = $params['workerName'] ?? null;

                    if (!isset($this->worker)) {
                        $connection = $params['connection'] ?? null;
                        $workersNum = $params['workersNum'] ?? 0;
                        $workerName = $params['workerName'] ?? null;
                        $queueName = $params['queueName'] ?? 'v1-'.$workerName;
                        $adapter = new Server($connection, $workersNum, $queueName);
                        $this->worker ??= new Worker($adapter);
                    }
                    $this->initWorker($services, $workerName);
                    break;
                default:
                    throw new Exception('Please provide which type of initialization you want to carry out.');
            }
        }
    }

    /**
     * Init HTTP service
     *
     * @param  Service  $service
     * @return void
     */
    protected function initHttp(array $services): void
    {
        foreach ($services as $service) {
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
                        $hook->alias($action->getHttpAliasPath());
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
    protected function initTasks(array $services): void
    {
        $cli = $this->cli;
        foreach ($services as $service) {
            foreach ($service->getActions() as $key => $action) {
                switch ($action->getType()) {
                    case Action::TYPE_INIT:
                        $hook = $cli->init();
                        break;
                    case Action::TYPE_ERROR:
                        $hook = $cli->error();
                        break;
                    case Action::TYPE_SHUTDOWN:
                        $hook = $cli->shutdown();
                        break;
                    case Action::TYPE_DEFAULT:
                    default:
                        $hook = $cli->task($key);
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
    protected function initWorker(array $services, string $workerName): void
    {
        $worker = $this->worker;
        foreach ($services as $service) {
            foreach ($service->getActions() as $key => $action) {
                if ($action->getType() == Action::TYPE_DEFAULT && strtolower($key) !== $workerName) {
                    continue;
                }
                switch ($action->getType()) {
                    case Action::TYPE_INIT:
                        $hook = $worker->init();
                        break;
                    case Action::TYPE_ERROR:
                        $hook = $worker->error();
                        break;
                    case Action::TYPE_SHUTDOWN:
                        $hook = $worker->shutdown();
                        break;
                    case Action::TYPE_WORKER_START:
                        $hook = $worker->workerStart();
                        break;
                    case Action::TYPE_DEFAULT:
                    default:
                        $hook = $worker->job();
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
     * Add module
     *
     * @param  Module  $module
     * @return self
     */
    public function addModule(Module $module): self
    {
        $this->modules[] = $module;

        return $this;
    }

    /**
     * Add Service
     *
     * @param  string  $key
     * @param  Service  $service
     * @return Platform
     */
    public function addService(string $key, Service $service): self
    {
        $this->core->addService($key, $service);

        return $this;
    }

    /**
     * Remove Service
     *
     * @param  string  $key
     * @return Platform
     */
    public function removeService(string $key): self
    {
        $this->core->removeService($key);

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
        return $this->core->getService($key);
    }

    /**
     * Get Services
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->core->getServices();
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

    /**
     * Get env
     *
     * Method for querying env parameters. If $key is not found $default value will be returned.
     *
     * @param  string  $key
     * @param  string|null  $default
     * @return mixed
     */
    public function getEnv(string $key, string $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}
