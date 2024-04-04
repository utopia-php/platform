<?php

namespace Utopia\Platform;

use Exception;
use Utopia\CLI\CLI;
use Utopia\Queue\Adapter\Swoole;
use Utopia\Queue\Server;

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

    protected Server $worker;

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
            switch ($type) {
                case Service::TYPE_HTTP:
                    $module->initHttp();
                    break;
                case Service::TYPE_TASK:
                    $this->cli ??= new CLI();
                    $module->initCLI($this->cli);
                    break;
                case Service::TYPE_GRAPHQL:
                    $module->initGraphQL();
                    break;
                case Service::TYPE_WORKER:
                    $workerName = $params['workerName'] ?? null;

                    if ($this->worker == null) {
                        $connection = $params['connection'] ?? null;
                        $workersNum = $params['workersNum'] ?? 0;
                        $queueName = $params['queueName'] ?? 'v1-'.$workerName;
                        $adapter = new Swoole($connection, $workersNum, $queueName);
                        $this->worker = new Server($adapter);
                    }
                    $module->initWorker($this->worker, $workerName);
                    break;
                default:
                    throw new Exception('Please provide which type of initialization you want to carry out.');
            }
        }
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
    public function getWorker(): Server
    {
        return $this->worker;
    }

    /**
     * Set the value of worker
     */
    public function setWorker(Server $worker): self
    {
        $this->worker = $worker;

        return $this;
    }
}
