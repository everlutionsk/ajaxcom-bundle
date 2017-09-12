<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use DM\AjaxCom\Handler;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ChangeUrl.
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ChangeUrl
{
    /** @var RequestStack */
    private $requestStack;
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function handle(Handler $handler)
    {

    }
}
