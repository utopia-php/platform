<?php

namespace Utopia\Platform;

use Utopia\Validator;
use Exception;

abstract class Action {
    protected string $httpMethod;
    protected string $httpPath;
    protected string $httpAliasPath;
    protected string $desc;
    protected array $httpAliasParams = [];
    protected array $groups = [];
    protected $callback;
    protected array $params = [];
    protected array $injections = [];
    protected array $labels = [];

    /**
     * Set the value of httpMethod
     * 
     * @param string $httpMethod
     * 
     * @return Action
     */
    public function http(String $path, string $method): self
    {
        $this->httpMethod = $method;
        $this->httpPath = $path;

        return $this;
    }

    /**
     * Get the value of httpPath
     */
    public function getHttpPath(): string
    {
        return $this->httpPath;
    }

    /**
     * Get the value of httpAliasPath
     */
    public function getHttpAliasPath(): string
    {
        return $this->httpAliasPath;
    }

    /**
     * Set the value of httpAliasPath
     */
    public function httpAlias(string $path, array $params =[]): self
    {
        $this->httpAliasPath = $path;
        $this->httpAliasParams = $params;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function desc(string $description): self
    {
        $this->desc = $description;

        return $this;
    }

    /**
     * Get the value of httpAliasParams
     */
    public function getHttpAliasParams(): array
    {
        return $this->httpAliasParams;
    }


    /**
     * Get the value of groups
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Set the value of groups
     */
    public function groups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get the value of callback
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Set the value of callback
     */
    public function setCallback($callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Get the value of params
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set the value of params
     */
    public function param(string $key, mixed $default, Validator|callable $validator, string $description = '', bool $optional = false, array $injections = []): self
    {
        $this->params[$key] = [
            'default' => $default,
            'validator' => $validator,
            'description' => $description,
            'optional' => $optional,
            'injections' => $injections,
            'value' => null,
            'order' => count($this->params) + count($this->injections),
        ];

        return $this;
    }

    /**
     * Get the value of injections
     */
    public function getInjections(): array
    {
        return $this->injections;
    }

    /**
     * Inject
     *
     * @param string $injection
     *
     * @throws Exception
     *
     * @return self
     */
    public function inject(string $injection): self
    {
        if (array_key_exists($injection, $this->injections)) {
            throw new Exception('Injection already declared for ' . $injection);
        }

        $this->injections[$injection] = [
            'name' => $injection,
            'order' => count($this->params) + count($this->injections),
        ];

        return $this;
    }

    /**
     * Get the value of labels
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Add Label
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function label(string $key, mixed $value): self
    {
        $this->labels[$key] = $value;

        return $this;
    }

    /**
     * Get the value of httpMethod
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }
}