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
    public function render($view, array $parameters = array(), Response $response = null): Response
    {
        $request = $this->get('request_stack')->getMasterRequest();

        if ($request->server->get(Ajaxcom::AJAX_COM_HEADER, false)) {
            return $this->get('ajaxcom.handler')->handle($view, $parameters);
        }

        return parent::render($view, $parameters, $response);
    }

    protected function renderAjaxBlock(string $id): self
    {
        $this->get('ajaxcom.mutation.add_blocks')->add($id);

        return $this;
    }

    protected function removeAjaxBlock(string $selector): self
    {
        $this->get('ajaxcom.mutation.remove_blocks')->add($selector);

        return $this;
    }

    protected function addCallback(string $functionName, array $parameters = []): self
    {
        $this->get('ajaxcom.mutation.callbacks')->add(new Callback($functionName, $parameters));

        return $this;
    }

    protected function replaceClass(string $selector, string $class): self
    {
        $this->get('ajaxcom.mutation.replace_class')->add($selector, $class);

        return $this;
    }

    protected function doNotChangeUrl(): self
    {
        $this->get('ajaxcom.mutation.change_url')->doNotChangeUrl();

        return $this;
    }

    abstract protected function get($id);
}
