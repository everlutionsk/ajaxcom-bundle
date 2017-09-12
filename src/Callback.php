<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle;

/**
 * Class Callback.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Callback
{
    /** @var string */
    private $function;
    /** @var array */
    private $parameters;
    /** @var int */
    private $priority;

    public function __construct(string $function, array $parameters = [], int $priority = 0)
    {
        $this->function = $function;
        $this->parameters = $parameters;
        $this->priority = $priority;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
