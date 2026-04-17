<?php

namespace Utopia\Platform\Scope;

trait HTTP
{
    protected ?string $httpMethod = null;

    protected ?string $httpPath = null;

    /**
     * @var array<string>
     */
    protected array $httpAliases = [];

    /**
     * Set Http path
     *
     * @param  string  $path
     * @return self
     */
    public function setHttpPath(string $path): self
    {
        $this->httpPath = $path;

        return $this;
    }

    /**
     * Set Http Method
     *
     * @param  string  $method
     * @return self
     */
    public function setHttpMethod(string $method): self
    {
        $this->httpMethod = $method;

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
     * Get the value of httpAliases
     *
     * @return array<string>
     */
    public function getHttpAliases(): array
    {
        return $this->httpAliases;
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

    /**
     * Append an httpAlias path. Can be called multiple times to register several aliases.
     *
     * @param  string  $path
     * @return self
     */
    public function httpAlias(string $path): self
    {
        $this->httpAliases[] = $path;

        return $this;
    }
}
