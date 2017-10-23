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

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function handle(Handler $ajax): Handler
    {
        /** @var AjaxCallback[] $callbacks */
        $callbacks = $this->session->get(self::SESSION_KEY, []);
        uasort($callbacks, [$this, 'sortByPriority']);

        foreach ($callbacks as $callback) {
            $ajax->callback($callback->getFunction(), $callback->getParameters());
        }

        $this->session->remove(self::SESSION_KEY);

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
