<?php

namespace Utopia\Platform;

use Utopia\App;

abstract class Platform {
    protected array $services = [];

    /**
     * Initialize Application
     *
     * @return void
     */
    public function init():void
    {
        foreach ($this->services as $key => $service) {
            switch($service->getType()) {
                case Service::TYPE_HTTP:
                    $this->initHttp($service);
            }
        }
    }

    /**
     * Init HTTP service
     *
     * @param Service $service
     * @return void
     */
    protected function initHttp(Service $service): void
    {
        foreach ($service->getActions() as $key => $action) {
            /** @var Action $action */
            $route = App::addRoute($action->getHttpMethod(), $action->getHttpPath());

            $route->groups($action->getGroups());
            $route->alias($action->getHttpAliasPath(), $action->getHttpAliasParams());

            foreach ($action->getParams() as $key => $param) {
                $route->param($key, $param['default'], $param['validator'], $param['description'], $param['optional'], $param['injections']);
            }

            foreach ($action->getInjections() as $injection) {
                $route->inject($injection);
            }

            foreach($action->getLabels() as $key => $label) {
                $route->label($key, $label);
            }

            $route->action($action->getCallback());
        }
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
        $this->services[$key] = $service;
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
     * @return Service|null
     */
    public function getService(string $key): ?Service
    {
        return $this->services[$key] ?? null;
    }


    /**
     * Get Services
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }
}
