<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;

class AppendBlocks implements MutatorInterface, RenderableInterface
{
    use RenderableTrait;

    /** @var RenderBlock */
    private $renderBlock;

    /** @var Block[] */
    private $blocks = [];

    public function __construct(RenderBlock $renderBlock)
    {
        $this->renderBlock = $renderBlock;
    }

    public function mutate(Handler $ajaxcom): Handler
    {
        foreach ($this->blocks as $block) {
            try {
                $html = $this->renderBlock->render($block, $this->view, $this->parameters);
                $ajaxcom->container('#'.$block->getId())->append($html);
            } catch (AjaxcomException $e) {
                continue;
            }
        }

        return $ajaxcom;
    }

    public function add(string $selector): self
    {
        $this->blocks[] = new Block($selector);

        return $this;
    }
}
