<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Service;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\Mutation\MutatorInterface;
use Everlution\AjaxcomBundle\Mutation\RenderableInterface;
use Everlution\AjaxcomBundle\Mutation\Container;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Ajaxcom.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Ajaxcom
{
    const AJAX_COM_HEADER = 'HTTP_X_AJAXCOM';
    const AJAX_COM_FRAGMENT_HEADER = 'HTTP_X-AJAXCOMFRAGMENT';
    const AJAX_COM_CACHE_CONTROL = ['Cache-Control' => 'no-cache,max-age=0,must-revalidate,no-store'];

    /** @var Handler */
    private $handler;
    /** @var Container */
    private $container;

    public function __construct(Handler $handler, Container $container)
    {
        $this->handler = $handler;
        $this->container = $container;
    }

    public function handle(string $view, array $parameters = []): JsonResponse
    {
        /** @var MutatorInterface $mutator */
        foreach ($this->container->getMutators() as $mutator) {
            if ($mutator instanceof RenderableInterface) {
                $mutator->setView($view);
                $mutator->setParameters($parameters);
            }

            $mutator->mutate($this->handler);
        }

        return new JsonResponse($this->handler->respond(), JsonResponse::HTTP_OK, self::AJAX_COM_CACHE_CONTROL);
    }
}
