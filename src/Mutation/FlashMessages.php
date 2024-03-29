<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class FlashMessages.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class FlashMessages implements MutatorInterface
{
    /** @var RenderBlock */
    private $renderBlock;
    /** @var RequestStack */
    private $requestStack;
    /** @var string */
    private $flashesTemplate;
    /** @var string */
    private $flashesBlockId;

    public function __construct(
        RenderBlock $renderBlock,
        RequestStack $requestStack,
        string $flashesTemplate,
        string $flashesBlockId
    ) {
        $this->renderBlock = $renderBlock;
        $this->requestStack = $requestStack;
        $this->flashesTemplate = $flashesTemplate;
        $this->flashesBlockId = $flashesBlockId;
    }

    public function mutate(Handler $ajax): Handler
    {
        $session = $this->requestStack->getSession();
        if (!$session instanceof Session) {
            throw new AjaxcomException('Bad session instance');
        }

        try {
            $messages = $this->renderBlock->render(
                (new Block($this->flashesBlockId))->refresh(),
                $this->flashesTemplate,
                ['flashes' => $session->/** @scrutinizer ignore-call */getFlashBag()->all()]
            );
            $ajax->container("#$this->flashesBlockId")->html($messages);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
