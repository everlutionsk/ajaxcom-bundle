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

    /** @var Block[] */
    private $addBlocks = [];
    /** @var string[] */
    private $removeBlocks = [];
    /** @var Callback[] */
    private $callbacks = [];
    /** @var bool */
    private $modal = false;

    public function __construct(
        Handler $handler,
        Session $session,
        \Twig_Environment $twig,
        UrlGeneratorInterface $router,
        string $flashesTemplate,
        string $flashesBlockId
    ) {
        $this->handler = $handler;
        $this->session = $session;
        $this->twig = $twig;
        $this->router = $router;
        $this->flashesTemplate = $flashesTemplate;
        $this->flashesBlockId = $flashesBlockId;
    }

    public function handle(string $view, array $parameters = [], Request $request): JsonResponse
    {
        $ajax = $this->handler;
        $ajax->modal('.modal')->close();
        if ($this->modal) {
            $ajax->modal($this->twig->render($view, $parameters));

            return new JsonResponse($ajax->respond(), JsonResponse::HTTP_OK, self::AJAX_COM_CACHE_CONTROL);
        }

        $ajax = $this->renderFlashMessages($ajax);
        $ajax = $this->removeBlock($ajax);
        $ajax = $this->addBlocks($ajax, $view, $parameters);
        $ajax = $this->addCallbacks($ajax);
        $ajax = $this->changeUrl($ajax, $request);

        return new JsonResponse($ajax->respond(), JsonResponse::HTTP_OK, self::AJAX_COM_CACHE_CONTROL);
    }

    /**
     * @param string $id
     *
     * @return Ajaxcom
     */
    public function removeAjaxBlock(string $id): self
    {
        $this->removeBlocks[] = $id;

        return $this;
    }

    public function renderModal()
    {
        $this->modal = true;
    }

    /**
     * @param string $id
     * @param array $callbacks
     *
     * @return Ajaxcom
     */
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
        $this->callbacks[] = $callback;

        return $this;
    }

    /**
     * @param Handler $ajax
     * @param Block $block
     *
     * @return Handler
     */
    private function addBlockCallbacks(Handler $ajax, Block $block): Handler
    {
        foreach ($block->getCallbacks() as $callback) {
            $ajax->callback($callback->getFunction(), $callback->getParameters());
        }

        return $ajax;
    }

    /**
     * @param Handler $ajax
     * @param Request $request
     *
     * @return Handler
     */
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

    /**
     * @param Handler $ajax
     *
     * @return Handler
     */
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

    /**
     * @param string $template
     * @param string $blockId
     * @param array $parameters
     *
     * @return string
     */
    private function renderBlock(string $template, string $blockId, array $parameters = []): string
    {
        return $this->twig->load($template)->renderBlock($blockId, $parameters);
    }

    /**
     * @param Handler $ajax
     *
     * @return Handler
     */
    private function removeBlock(Handler $ajax): Handler
    {
        foreach ($this->removeBlocks as $blockId) {
            $ajax->container(sprintf('#%s', $blockId))->html('');
        }

        return $ajax;
    }

    /**
     * @param Handler $ajax
     * @param string $view
     * @param array $parameters
     *
     * @return Handler
     */
    private function addBlocks(Handler $ajax, string $view, array $parameters): Handler
    {
        $template = $template = $this->twig->load($view);

        foreach ($this->addBlocks as $block) {
            $html = $template->renderBlock($block->getId(), $parameters);
            $ajax->container(sprintf('#%s', $block->getId()))->html($html);
            $ajax = $this->addBlockCallbacks($ajax, $block);
        }

        return $ajax;
    }

    /**
     * @param Handler $ajax
     *
     * @return Handler
     */
    private function addCallbacks(Handler $ajax): Handler
    {
        foreach ($this->callbacks as $callback) {
            $ajax->callback($callback->getFunction(), $callback->getParameters());
        }

        return $ajax;
    }
}
