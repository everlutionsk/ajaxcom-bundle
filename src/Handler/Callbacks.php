<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\DataObject\Callback as AjaxCallback;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Callbacks.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Callbacks
{
    const SESSION_KEY = 'ajaxcom/callbacks';

    /** @var Session */
    private $session;
    /** @var AjaxCallback[] */
    private $callbacks = [];

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function handle(Handler $ajax): Handler
    {
        uasort($this->callbacks, [$this, 'sortByPriority']);
        foreach ($this->callbacks as $callback) {
            $ajax->callback($callback->getFunction(), $callback->getParameters());
        }

        return $ajax;
    }

    public function add(AjaxCallback $callback): self
    {
        $callbacks = $this->session->get(self::SESSION_KEY, []);
        $callbacks[] = $callback;
        $this->session->set(self::SESSION_KEY, $callbacks);

        return $this;
    }

    private function sortByPriority(AjaxCallback $a, AjaxCallback $b): int
    {
        return $a->getPriority() <=> $b->getPriority();
    }
}
