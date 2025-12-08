<?php

namespace Utopia\Platform\Scope;

trait HTTP
{
    protected ?string $httpMethod = null;

    protected ?string $httpPath = null;

    protected ?string $httpAliasPath = null;

    /**
     * @var array<string, mixed>
     */
    protected array $httpAliasParams = [];

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
     * @return string|null
     */
    public function getHttpPath(): ?string
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
     * Get the value of httpAliasParams
     *
     * @return array<string, mixed>
     */
    public function getHttpAliasParams(): array
    {
        return $this->httpAliasParams;
    }

    /**
     * Get the value of httpMethod
     *
     * @return string|null
     */
    public function getHttpMethod(): ?string
    {
        return $this->httpMethod;
    }

    /**
     * Set httpAlias path and params
     *
     * @param  string  $path
     * @param  array<string, mixed>  $params
     * @return self
     */
    public function httpAlias(string $path, array $params = []): self
    {
        $this->httpAliasPath = $path;
        $this->httpAliasParams = $params;

        return $this;
    }
}
