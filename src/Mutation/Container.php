<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

/**
 * Class Container.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Container
{
    /** @var MutatorInterface[] */
    private $mutators = [];

    public function __construct(array $mutators = [])
    {
        array_map([$this, 'add'], $mutators);
    }

    public function add(MutatorInterface $mutator): void
    {
        $this->mutators[] = $mutator;
    }

    /**
     * @return MutatorInterface[]
     */
    public function getMutators(): array
    {
        return $this->mutators;
    }
}
