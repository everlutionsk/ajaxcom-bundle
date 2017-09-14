<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use DM\AjaxCom\Handler;

/**
 * Class ReplaceClass.
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceClass
{
    private $selectors = [];

    public function handle(Handler $ajax): Handler
    {
        foreach ($this->selectors as $selector => $class) {
            $ajax->container($selector)->attr('class', $class);
        }

        return $ajax;
    }

    public function add(string $selector, string $class): self
    {
        $this->selectors[$selector] = $class;

        return $this;
    }
}
