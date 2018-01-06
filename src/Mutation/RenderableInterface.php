<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

/**
 * Interface RenderableInterface.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
interface RenderableInterface
{
    public function setView(string $view): void;

    public function setParameters(array $parameters): void;
}
