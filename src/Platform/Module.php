<?php

namespace Utopia\Platform;

use Exception;
use Utopia\App;
use Utopia\CLI\CLI;
use Utopia\Queue\Server;
use Utopia\Route;

class Module
{
    protected array $services = [
        'all' => [],
        Service::TYPE_TASK => [],
        Service::TYPE_HTTP => [],
        Service::TYPE_GRAPHQL => [],
        Service::TYPE_WORKER => [],
    ];

    /**
     * Init HTTP service
     *
     * @param  Service  $service
     * @return void
     */
    public function initHttp(): void
    {
        foreach ($this->services[Service::TYPE_HTTP] as $service) {
            foreach ($service->getActions() as $action) {
                /** @var Action $action */
                switch ($action->getType()) {
                    case Action::TYPE_INIT:
                        $hook = App::init();
                        break;
                    case Action::TYPE_ERROR:
                        $hook = App::error();
                        break;
                    case Action::TYPE_OPTIONS:
                        $hook = App::options();
                        break;
                    case Action::TYPE_SHUTDOWN:
                        $hook = App::shutdown();
                        break;
                    case Action::TYPE_DEFAULT:
                    default:
                        $hook = App::addRoute($action->getHttpMethod(), $action->getHttpPath());
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
    public function initCLI(CLI $cli): void
    {
        foreach ($this->services[Service::TYPE_TASK] as $service) {
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
    public function initWorker(Server $worker, string $workerName): void
    {
        foreach ($this->services[Service::TYPE_WORKER] as $service) {
            foreach ($service->getActions() as $key => $action) {
                if (! str_contains(strtolower($key), $workerName)) {
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
    public function initGraphQL(): void
    {
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
     * @param  string  $key
     * @return Service|null
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
     * @return array
     */
    public function getServices(): array
    {
        return $this->services['all'];
    }
}
