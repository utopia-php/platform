<?php

namespace Utopia\Platform;

use Utopia\Validator;
use Exception;
use Utopia\Platform\Scope\HTTP;

abstract class Action
{
    use HTTP;

    /**
     * Request method constants
     */
    public const HTTP_REQUEST_METHOD_GET        = 'GET';
    public const HTTP_REQUEST_METHOD_POST       = 'POST';
    public const HTTP_REQUEST_METHOD_PUT        = 'PUT';
    public const HTTP_REQUEST_METHOD_PATCH      = 'PATCH';
    public const HTTP_REQUEST_METHOD_DELETE     = 'DELETE';
    public const HTTP_REQUEST_METHOD_OPTIONS    = 'OPTIONS';
    public const HTTP_REQUEST_METHOD_HEAD       = 'HEAD';

    public const TYPE_DEFAULT           = 'Default';
    public const TYPE_INIT              = 'Init';
    public const TYPE_SHUTDOWN          = 'Shutdown';
    public const TYPE_ERROR             = 'Error';
    public const TYPE_OPTIONS           = 'Options';

    protected ?string $desc = null;
    protected array $groups = [];
    protected $callback;
    protected array $options = [];
    protected array $params = [];
    protected array $injections = [];
    protected array $labels = [];
    protected string $type = self::TYPE_DEFAULT;

    /**
     * Set Type
     *
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDesc(): ?string
    {
        return $this->desc;
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
        $param = [
            'default' => $default,
            'validator' => $validator,
            'description' => $description,
            'optional' => $optional,
            'injections' => $injections
        ];
        $this->options['param:' . $key] = array_merge($param, ['type' => 'param']);
        $this->params[$key] = $param;

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

        $this->options['injection:' . $injection] = [
            'name' => $injection,
            'type' => 'injection'
        ];
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
     * Get Http Options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
