<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;
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
    /** @var Session */
    private $session;
    /** @var string */
    private $flashesTemplate;
    /** @var string */
    private $flashesBlockId;

    public function __construct(RenderBlock $renderBlock, Session $session, string $flashesTemplate, string $flashesBlockId)
    {
        $this->renderBlock = $renderBlock;
        $this->session = $session;
        $this->flashesTemplate = $flashesTemplate;
        $this->flashesBlockId = $flashesBlockId;
    }

    public function mutate(Handler $ajax): Handler
    {
        $flashBag = $this->session->getFlashBag();

        if (empty($flashBag->peekAll())) {
            return $ajax;
        }

        try {
            $messages = $this->renderBlock->render(
                new Block($this->flashesBlockId),
                $this->flashesTemplate,
                ['flashes' => $flashBag->all()]
            );
            $ajax->container("#$this->flashesBlockId")->html($messages);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
