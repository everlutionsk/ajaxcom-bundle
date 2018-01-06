<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ChangeUrl.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ChangeUrl implements MutatorInterface
{
    /** @var Request */
    private $request;
    /** @var UrlGeneratorInterface */
    private $router;
    /** @var bool */
    private $changeUrl = true;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->router = $router;
        $this->request = $requestStack->getMasterRequest();
    }

    public function mutate(Handler $ajax): Handler
    {
        if (false === $this->changeUrl) {
            return $ajax;
        }

        $ajax->changeUrl(
            $this->router->generate(
                $this->request->attributes->get('_route'),
                array_merge($this->request->attributes->get('_route_params'), $this->request->query->all())
            )
        );

        return $ajax;
    }

    public function doNotChangeUrl(): self
    {
        $this->changeUrl = false;

        return $this;
    }
}
