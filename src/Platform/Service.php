<?php

namespace Utopia\Platform;

abstract class Service
{
    public const TYPE_HTTP = 'http';

    public const TYPE_GRAPHQL = 'GraphQL';

    public const TYPE_TASK = 'Task';

    public const TYPE_CLI = 'CLI';

    public const TYPE_WORKER = 'Worker';

    protected array $actions;

    protected string $type;

    /**
     * Set Type
     *
     * @param  string  $type
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
     * @param  string  $key
     * @param  Action  $action
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
     * @param  string  $key
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
     * @param  string  $key
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
}
