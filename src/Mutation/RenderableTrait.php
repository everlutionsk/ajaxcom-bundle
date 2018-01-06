<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

/**
 * Trait RenderableTrait.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
trait RenderableTrait
{
    /** @var string */
    private $view;
    /** @var array */
    private $parameters;

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
