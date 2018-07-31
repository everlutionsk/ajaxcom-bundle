<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class AppendContent.
 * @author Robert Stribrnsky <robert.stribrnsky@everlution.sk>
 */
class AppendContent implements MutatorInterface, RenderableInterface
{
    use RenderableTrait;

    /** @var Block */
    private $block;
    /** @var RenderBlock */
    private $renderBlock;

    public function __construct(RenderBlock $renderBlock)
    {
        $this->renderBlock = $renderBlock;
    }

    public function mutate(Handler $ajaxcom): Handler
    {
        try {
            $html = $this->renderBlock->render($this->block, $this->view, $this->parameters);
        } catch (AjaxcomException $exception) {

        }

        $ajaxcom->container($this->block->getId())->append($html);

        return $ajaxcom;
    }

    public function add(string $id): self
    {
        $this->block = new Block($id);

        return $this;
    }
}
