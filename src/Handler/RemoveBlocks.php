<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use Everlution\Ajaxcom\Handler;

/**
 * Class RemoveBlocks.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class RemoveBlocks
{
    /** @var string[] */
    private $removeBlocks = [];

    public function handle(Handler $ajax): Handler
    {
        foreach ($this->removeBlocks as $selector) {
            $ajax->container($selector)->remove();
        }

        return $ajax;
    }

    public function add(string $selector): self
    {
        $this->removeBlocks[] = $selector;

        return $this;
    }
}
