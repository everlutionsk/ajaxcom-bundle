<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\DataObject\Callback as AjaxCallback;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Callbacks.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Callbacks implements MutatorInterface
{
    const SESSION_KEY = 'ajaxcom/callbacks';

    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function mutate(Handler $ajax): Handler
    {
        /** @var AjaxCallback[] $callbacks */
        $callbacks = $this->requestStack->getSession()->get(self::SESSION_KEY, []);
        uasort($callbacks, [$this, 'sortByPriority']);

        foreach ($callbacks as $callback) {
            $ajax->callback($callback->getFunction(), $callback->getParameters());
        }

        $this->requestStack->getSession()->remove(self::SESSION_KEY);

        return $ajax;
    }

    public function add(AjaxCallback $callback): self
    {
        $callbacks = $this->requestStack->getSession()->get(self::SESSION_KEY, []);
        $callbacks[] = $callback;
        $this->requestStack->getSession()->set(self::SESSION_KEY, $callbacks);

        return $this;
    }

    private function sortByPriority(AjaxCallback $a, AjaxCallback $b): int
    {
        return $a->getPriority() <=> $b->getPriority();
    }
}
