<?php

namespace Utopia\Platform;

use Utopia\Validator;
use Exception;

abstract class Action
{
    protected ?string $httpMethod = null;
    protected ?string $httpPath = null;
    protected ?string $httpAliasPath = null;
    protected ?string $desc = null;
    protected array $httpAliasParams = [];
    protected array $groups = [];
    protected $callback;
    protected array $params = [];
    protected array $injections = [];
    protected array $labels = [];

   /**
    * Set Http method and path
    *
    * @param String $path
    * @param string $method
    * @return self
    */
    public function http(string $method, string $path): self
    {
        $this->httpMethod = $method;
        $this->httpPath = $path;

        return $this;
    }

    /**
     * Get httpPath
     *
     * @return string
     */
    public function getHttpPath(): string
    {
        return $this->httpPath;
    }

    /**
     * Get the value of httpAliasPath
     *
     * @return string
     */
    public function getHttpAliasPath(): ?string
    {
        return $this->httpAliasPath;
    }

    /**
     * Set httpAlias path and params
     *
     * @param string $path
     * @param array $params
     * @return self
     */
    public function httpAlias(string $path, array $params = []): self
    {
        $this->httpAliasPath = $path;
        $this->httpAliasParams = $params;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string $description
     *
     * @return self
     */
    public function desc(string $description): self
    {
        $this->desc = $description;

        return $this;
    }

    /**
     * Get the value of httpAliasParams
     *
     * @return array
     */
    public function getHttpAliasParams(): array
    {
        return $this->httpAliasParams;
    }


    /**
     * Get the value of groups
     *
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Set Groups
     *
     * @param array $groups
     * @return self
     */
    public function groups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get the value of callback
     *
     * @return mixed
     */
    public function getCallback(): mixed
    {
        return $this->callback;
    }

    /**
     * Set Callback
     *
     * @param mixed $callback
     * @return self
     */
    public function callback(mixed $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Get the value of params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set Param
     *
     * @param string $key
     * @param mixed $default
     * @param Validator|callable $validator
     * @param string $description
     * @param boolean $optional
     * @param array $injections
     * @return self
     */
    public function param(string $key, mixed $default, Validator|callable $validator, string $description = '', bool $optional = false, array $injections = []): self
    {
        $this->params[$key] = [
            'default' => $default,
            'validator' => $validator,
            'description' => $description,
            'optional' => $optional,
            'injections' => $injections
        ];

        return $this;
    }

    /**
     * Get the value of injections
     *
     * @return array
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

        $this->injections[] = $injection;

        return $this;
    }

    /**
     * Get the value of labels
     *
     * @return array
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
     * @return self
     */
    public function label(string $key, mixed $value): self
    {
        $this->labels[$key] = $value;

        return $this;
    }

    /**
     * Get the value of httpMethod
     *
     * @return string
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }
}
