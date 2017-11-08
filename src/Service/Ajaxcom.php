<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Service;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\Handler\AddBlocks;
use Everlution\AjaxcomBundle\Handler\Callbacks;
use Everlution\AjaxcomBundle\Handler\ChangeUrl;
use Everlution\AjaxcomBundle\Handler\FlashMessages;
use Everlution\AjaxcomBundle\Handler\ReplaceClass;
use Everlution\AjaxcomBundle\Handler\ReplaceJavaScripts;
use Everlution\AjaxcomBundle\Handler\ReplaceMetaTags;
use Everlution\AjaxcomBundle\Handler\ReplaceStyleSheets;
use Everlution\AjaxcomBundle\Handler\ReplaceTitle;
use Everlution\AjaxcomBundle\Handler\RemoveBlocks;
use Everlution\AjaxcomBundle\DataObject\Callback as AjaxCallback;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Ajaxcom.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Ajaxcom
{
    const AJAX_COM_HEADER = 'HTTP_X_AJAXCOM';
    const AJAX_COM_CACHE_CONTROL = ['Cache-Control' => 'no-cache,max-age=0,must-revalidate,no-store'];

    /** @var Handler */
    private $handler;
    /** @var ReplaceJavaScripts */
    private $replaceJavaScripts;
    /** @var ReplaceStyleSheets */
    private $replaceStyleSheets;
    /** @var ReplaceMetaTags */
    private $replaceMetaTags;
    /** @var ReplaceTitle */
    private $replaceTitle;
    /** @var FlashMessages */
    private $flashMessages;
    /** @var RemoveBlocks */
    private $removeBlocks;
    /** @var AddBlocks */
    private $addBlocks;
    /** @var Callbacks */
    private $callbacks;
    /** @var ChangeUrl */
    private $changeUrl;
    /** @var ReplaceClass */
    private $replaceClass;

    public function __construct(
        Handler $handler,
        ReplaceJavaScripts $replaceJavaScripts,
        ReplaceStyleSheets $replaceStyleSheets,
        ReplaceMetaTags $replaceMetaTags,
        ReplaceTitle $replaceTitle,
        FlashMessages $flashMessages,
        RemoveBlocks $removeBlocks,
        AddBlocks $addBlocks,
        Callbacks $callbacks,
        ChangeUrl $changeUrl,
        ReplaceClass $replaceClass
    ) {
        $this->handler = $handler;
        $this->replaceJavaScripts = $replaceJavaScripts;
        $this->replaceStyleSheets = $replaceStyleSheets;
        $this->replaceMetaTags = $replaceMetaTags;
        $this->replaceTitle = $replaceTitle;
        $this->flashMessages = $flashMessages;
        $this->removeBlocks = $removeBlocks;
        $this->addBlocks = $addBlocks;
        $this->callbacks = $callbacks;
        $this->changeUrl = $changeUrl;
        $this->replaceClass = $replaceClass;
    }

    public function handle(string $view, array $parameters = []): JsonResponse
    {
        $ajax = $this->handler;

        $ajax = $this->replaceStyleSheets->handle($ajax, $view, $parameters);
        $ajax = $this->replaceMetaTags->handle($ajax, $view, $parameters);
        $ajax = $this->replaceTitle->handle($ajax, $view, $parameters);
        $ajax = $this->flashMessages->handle($ajax);
        $ajax = $this->removeBlocks->handle($ajax);
        $ajax = $this->addBlocks->handle($ajax, $view, $parameters);
        $ajax = $this->replaceClass->handle($ajax);
        $ajax = $this->replaceJavaScripts->handle($ajax, $view, $parameters);
        $ajax = $this->callbacks->handle($ajax);
        $ajax = $this->changeUrl->handle($ajax);

        return new JsonResponse($ajax->respond(), JsonResponse::HTTP_OK, self::AJAX_COM_CACHE_CONTROL);
    }

    public function renderBlock(string $id): self
    {
        $this->addBlocks->add($id);

        return $this;
    }

    public function removeBlock(string $selector): self
    {
        $this->removeBlocks->add($selector);

        return $this;
    }

    public function addCallback(AjaxCallback $callback): self
    {
        $this->callbacks->add($callback);

        return $this;
    }

    public function replaceClass(string $selector, string $class): self
    {
        $this->replaceClass->add($selector, $class);

        return $this;
    }
}
