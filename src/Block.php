<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle;

/**
 * Class Block.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Block
{
    /** @var string */
    private $id;
    /** @var array */
    private $callbacks;

    public function __construct(string $id, array $callbacks = [])
    {
        $this->id = $id;

        $this->callbacks = [];
        foreach ($callbacks as $callback) {
            $this->addCallback($callback);
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Callback[]
     */
    public function getCallbacks(): array
    {
        uasort($this->callbacks, [$this, 'sortByPriority']);

        return $this->callbacks;
    }

    /**
     * @param $callback
     *
     * @return Block
     */
    public function addCallback(Callback $callback): self
    {
        $this->callbacks[] = $callback;

        return $this;
    }

    /**
     * @param Callback $a
     * @param Callback $b
     *
     * @return int
     */
    private function sortByPriority(Callback $a, Callback $b): int
    {
        return $a->getPriority() <=> $b->getPriority();
    }
}
