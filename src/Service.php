<?php

namespace Utopia\Platform;

abstract class Service {
    protected array $actions;
    protected string $type;

    public function setType(string $type): Service
    {
        $this->type = $type;
        return $this;
    }

    public function addAction(string $key, Action $service): Service
    {
        $this->actions[$key] = $service;
        return $this;
    }

    public function removeAction(string $key): Service
    {
        unset($this->actions[$key]);
        return $this;
    }

    public function getAction(string $key): ?Action
    {
        return $this->actions[$key] ?? null;
    }

    public function getActions(): array
    {
        return $this->actions;
    }
}