<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use DM\AjaxCom\Handler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ChangeUrl.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ChangeUrl
{
    /** @var Request */
    private $request;
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->router = $router;
        $this->request = $requestStack->getMasterRequest();
    }

    public function handle(Handler $ajax): Handler
    {
        $ajax->changeUrl(
            $this->router->generate(
                $this->request->attributes->get('_route'),
                $this->request->attributes->get('_route_params')
            )
        );

        return $ajax;
    }
}
