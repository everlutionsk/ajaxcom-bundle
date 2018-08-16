<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\Service\Ajaxcom;
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
    private $changeUrl;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router, bool $changeUrl)
    {
        $this->router = $router;
        $this->request = $requestStack->getMasterRequest();
        $this->changeUrl = $changeUrl;
    }

    public function mutate(Handler $ajax): Handler
    {
        if (false === $this->changeUrl) {
            return $ajax;
        }

        $ajax->changeUrl(
            $this->router->generate(
                $this->request->attributes->get('_route'),
                array_merge(
                    $this->request->attributes->get('_route_params'),
                    $this->request->query->all(),
                    $this->getFragment()
                )
            )
        );

        return $ajax;
    }

    public function doNotChangeUrl(): self
    {
        $this->changeUrl = false;

        return $this;
    }

    private function getFragment(): array
    {
        return ['_fragment' => $this->request->server->get(Ajaxcom::AJAX_COM_FRAGMENT_HEADER)];
    }
}
