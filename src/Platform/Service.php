<?php

namespace Utopia\Platform;

use Utopia\Platform\Scope\HttpService;

abstract class Service
{
    public const TYPE_HTTP = 'http';
    public const TYPE_GRAPHQL = 'GraphQL';
    public const TYPE_CLI = 'CLI';

    protected array $actions;
    protected string $type;
    protected array $initHooks = [];
    protected array $shutdownHooks = [];
    protected array $errorHooks = [];
    protected array $optionHooks = [];

    /**
     * Set Type
     *
     * @param string $type
     * @return Service
     */
    public function setType(string $type): Service
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Add
     *
     * @param string $key
     * @param Action $action
     * @return Service
     */
    public function addAction(string $key, Action $action): Service
    {
        $this->actions[$key] = $action;
        return $this;
    }

    /**
     * Remove Action
     *
     * @param string $key
     * @return Service
     */
    public function removeAction(string $key): Service
    {
        unset($this->actions[$key]);
        return $this;
    }

    /**
     * Get Action
     *
     * @param string $key
     * @return Action|null
     */
    public function getAction(string $key): ?Action
    {
        return $this->actions[$key] ?? null;
    }

    /**
     * Get Actions
     *
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Add Init Hook
     *
     * @param Action $hook
     * @return void
     */
    public function addInitHook(Action $hook): static
    {
        $initHooks[] = $hook;
        return $this;
    }

    /**
     * Add Shutdown Hook
     *
     * @param Action $hook
     * @return static
     */
    public function addShutdownHook(Action $hook): static
    {
        $shutdownHooks[] = $hook;
        return $this;
    }

    /**
     * Add Option Hook
     *
     * @param Action $hook
     * @return static
     */
    public function addOptionHook(Action $hook): static
    {
        $optionHooks[] = $hook;
        return $this;
    }

    /**
     * Add Error Hook
     *
     * @param Action $hook
     * @return static
     */
    public function addErrorHook(Action $hook): static
    {
        $errorHooks[] = $hook;
        return $this;
    }

    /**
     * Get Hooks
     *
     * @return array
     */
    public function getHooks(): array
    {
        return [
            'init' => $this->initHooks,
            'error' => $this->errorHooks,
            'shutdown' => $this->shutdownHooks,
            'option' => $this->optionHooks
        ];
    }
}
