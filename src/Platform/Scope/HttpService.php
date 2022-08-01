<?php

namespace Utopia\Platform\Scope;

use Utopia\Platform\Action;

trait HttpService
{
    protected array $initHooks = [];
    protected array $shutdownHooks = [];
    protected array $errorHooks = [];
    protected array $optionHooks = [];

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
