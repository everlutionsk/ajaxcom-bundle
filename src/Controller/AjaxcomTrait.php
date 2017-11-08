<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Controller;

use Everlution\AjaxcomBundle\DataObject\Callback;
use Everlution\AjaxcomBundle\Service\Ajaxcom;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
trait AjaxcomTrait
{
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $request = $this->get('request_stack')->getMasterRequest();

        if ($request->server->get(Ajaxcom::AJAX_COM_HEADER, false)) {
            return $this->get('ajaxcom.handler')->handle($view, $parameters);
        }

        return parent::render($view, $parameters, $response);
    }

    protected function renderAjaxBlock(string $id): self
    {
        $this->get('ajaxcom.handler')->renderBlock($id);

        return $this;
    }

    protected function removeAjaxBlock(string $selector): self
    {
        $this->get('ajaxcom.handler')->removeBlock($selector);

        return $this;
    }

    protected function addCallback(string $functionName, array $parameters = []): self
    {
        $this->get('ajaxcom.handler')->addCallback(new Callback($functionName, $parameters));

        return $this;
    }

    protected function replaceClass(string $selector, string $class): self
    {
        $this->get('ajaxcom.handler')->replaceClass($selector, $class);

        return $this;
    }

    protected function doNotChangeUrl(): self
    {
        $this->get('ajaxcom.handler.change_url')->doNotChangeUrl();

        return $this;
    }
}
