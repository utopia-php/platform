<?php

namespace Utopia\Platform;

use Utopia\App;

abstract class Platform {
    protected array $services = [];

    public function init():void
    {
        foreach ($this->services as $key => $service) {
            if($service->getType() === Service::TYPE_HTTP) {
                foreach ($service->getActions() as $key => $action) {
                    /** @var Action $action */
                    switch($action->getHttpMethod()) {
                        // should I make App::addRoute(method, path) public, it's protected now
                        case 'post':
                            $route = App::post($action->getHttpPath());
                        case 'get':
                        default:
                            $route = App::get($action->getHttpPath());
                            break;
                    }

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
        }
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
