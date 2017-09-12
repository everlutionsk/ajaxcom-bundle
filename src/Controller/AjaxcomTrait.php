<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Controller;

use Everlution\AjaxcomBundle\Callback;
use Everlution\AjaxcomBundle\Ajaxcom;
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
        $request = $this->get('request_stack')->getCurrentRequest();

        if ($request->server->get(Ajaxcom::AJAX_COM_HEADER, false)) {
            return $this->get('ajaxcom.handler')->handle($view, $parameters, $request);
        }

        return parent::render($view, $parameters, $response);
    }

    protected function renderAjaxBlock(string $id, array $callbacks = []): self
    {
        $this->get('ajaxcom.handler')->renderAjaxBlock($id, $callbacks);

        return $this;
    }

    protected function removeAjaxBlock(string $id): self
    {
        $this->get('ajaxcom.handler')->removeAjaxBlock($id);

        return $this;
    }

    protected function addCallback(string $function, array $parameters = []): self
    {
        $this->get('ajaxcom.handler')->addCallback(new Callback($function, $parameters));

        return $this;
    }

    protected function renderModal()
    {
        $this->get('ajaxcom.handler')->renderModal();

        return $this;
    }
}
