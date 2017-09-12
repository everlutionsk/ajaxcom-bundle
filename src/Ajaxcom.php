<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle;

use DM\AjaxCom\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Ajaxcom.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Ajaxcom
{
    const AJAXCOM_CALLBACKS = 'ajaxcom/callbacks';
    const AJAX_COM_HEADER = 'HTTP_X_AJAXCOM';
    const AJAX_COM_CACHE_CONTROL = ['Cache-Control' => 'no-cache,max-age=0,must-revalidate,no-store'];

    /** @var Handler */
    private $handler;
    /** @var Session */
    private $session;
    /** @var \Twig_Environment */
    private $twig;
    /** @var UrlGeneratorInterface */
    private $router;

    /** @var string */
    private $flashesTemplate;
    /** @var string */
    private $flashesBlockId;

    /** @var string */
    private $persistentClass;
    /** @var Block[] */
    private $addBlocks = [];
    /** @var string[] */
    private $removeBlocks = [];
    /** @var bool */
    private $modal = false;

    public function __construct(
        Handler $handler,
        Session $session,
        \Twig_Environment $twig,
        UrlGeneratorInterface $router,
        string $flashesTemplate,
        string $flashesBlockId,
        string $persistentClass,
        array $blocksToRender = []
    ) {
        $this->handler = $handler;
        $this->session = $session;
        $this->twig = $twig;
        $this->router = $router;
        $this->flashesTemplate = $flashesTemplate;
        $this->flashesBlockId = $flashesBlockId;
        $this->persistentClass = $persistentClass;

        foreach ($blocksToRender as $block) {
            $this->renderAjaxBlock($block);
        }
    }

    public function handle(string $view, array $parameters = [], Request $request): JsonResponse
    {
        $ajax = $this->handler;
        $ajax->modal('.modal')->close();
        if ($this->modal) {
            $ajax->modal($this->twig->render($view, $parameters));

            return new JsonResponse($ajax->respond(), JsonResponse::HTTP_OK, self::AJAX_COM_CACHE_CONTROL);
        }

        $ajax = $this->replaceJavaScripts($ajax, $view, $parameters);
        $ajax = $this->replaceStyleSheets($ajax, $view, $parameters);
        $ajax = $this->replaceMetaTags($ajax, $view, $parameters);
        $ajax = $this->renderFlashMessages($ajax);
        $ajax = $this->removeBlock($ajax);
        $ajax = $this->addBlocks($ajax, $view, $parameters);
        $ajax = $this->addCallbacks($ajax);
        $ajax = $this->changeUrl($ajax, $request);

        return new JsonResponse($ajax->respond(), JsonResponse::HTTP_OK, self::AJAX_COM_CACHE_CONTROL);
    }

    public function removeAjaxBlock(string $selector): self
    {
        $this->removeBlocks[] = $selector;

        return $this;
    }


    public function renderModal()
    {
        $this->modal = true;
    }

    public function renderAjaxBlock(string $id, array $callbacks = []): self
    {
        $ajaxCallbacks = [];
        foreach ($callbacks as $function => $parameters) {
            $ajaxCallbacks[] = new Callback($function, $parameters);
        }

        $this->addBlocks[] = new Block($id, $ajaxCallbacks);

        return $this;
    }

    public function addCallback(Callback $callback)
    {
        $callbacks = $this->session->get(self::AJAXCOM_CALLBACKS, []);
        $callbacks[] = $callback;
        $this->session->set(self::AJAXCOM_CALLBACKS, $callbacks);

        return $this;
    }

    private function addBlockCallbacks(Handler $ajax, Block $block): Handler
    {
        foreach ($block->getCallbacks() as $callback) {
            $ajax->callback($callback->getFunction(), $callback->getParameters());
        }

        return $ajax;
    }

    private function changeUrl(Handler $ajax, Request $request): Handler
    {
        $ajax->changeUrl(
            $this->router->generate(
                $request->attributes->get('_route'),
                $request->attributes->get('_route_params')
            )
        );

        return $ajax;
    }

    private function renderFlashMessages(Handler $ajax): Handler
    {
        $flashBag = $this->session->getFlashBag();

        if (false === empty($flashBag->peekAll())) {
            $messages = $this->renderBlock(
                $this->flashesTemplate,
                $this->flashesBlockId,
                ['flashes' => $flashBag->all()]
            );
            $ajax->container("#$this->flashesBlockId")->html($messages);
        }

        return $ajax;
    }

    private function renderBlock(string $template, string $blockId, array $parameters = []): string
    {
        return $this->twig->load($template)->renderBlock($blockId, $parameters);
    }

    private function removeBlock(Handler $ajax): Handler
    {
        foreach ($this->removeBlocks as $selector) {
            $ajax->container($selector)->remove();
        }

        return $ajax;
    }

    private function addBlocks(Handler $ajax, string $view, array $parameters): Handler
    {
        $template = $template = $this->twig->load($view);

        foreach ($this->addBlocks as $block) {
            if (false === $template->hasBlock($block->getId())) {
                continue;
            }

            $html = $template->renderBlock($block->getId(), $parameters);

            if (empty($html)) {
                continue;
            }

            $ajax->container(sprintf('#%s', $block->getId()))->html($html);
            $ajax = $this->addBlockCallbacks($ajax, $block);
        }

        return $ajax;
    }

    private function addCallbacks(Handler $ajax): Handler
    {
        $callbacks = $this->session->get(self::AJAXCOM_CALLBACKS, []);
        foreach ($callbacks as $callback) {
            $ajax->callback($callback->getFunction(), $callback->getParameters());
        }
        $this->session->remove(self::AJAXCOM_CALLBACKS);

        return $ajax;
    }

    private function replaceJavaScripts(Handler $ajax, string $view, array $parameters): Handler
    {
        $ajax->container(sprintf('script:not(.%s, [nonce])', $this->persistentClass))->remove();
        $javaScripts = $this->renderBlock($view, 'javascripts', $parameters);
        $ajax->container('script:last-of-type')->append($javaScripts);

        return $ajax;
    }

    private function replaceStyleSheets(Handler $ajax, string $view, array $parameters): Handler
    {
        $ajax->container(sprintf('style:not(.%s, [nonce])', $this->persistentClass))->remove();
        $styleSheets = $this->renderBlock($view, 'stylesheets', $parameters);
        $ajax->container('style:last-of-type')->append($styleSheets);

        return $ajax;
    }

    private function replaceMetaTags(Handler $ajax, string $view, array $parameters): Handler
    {
        $ajax->container(sprintf('meta:not(.%s)', $this->persistentClass))->remove();
        $metaTags = $this->renderBlock($view, 'metatags', $parameters);
        $ajax->container('meta:last-of-type')->append($metaTags);

        $title = $this->renderBlock($view, 'title', $parameters);
        $ajax->container('title')->html($title);

        return $ajax;
    }
}
