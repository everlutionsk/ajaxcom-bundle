<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use DM\AjaxCom\Handler;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class AddBlocks.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class AddBlocks
{
    /** @var RenderBlock */
    private $renderBlock;
    /** @var Block[] */
    private $blocks;

    public function __construct(RenderBlock $renderBlock, array $blocksToRender = [])
    {
        $this->renderBlock = $renderBlock;

        foreach ($blocksToRender as $id) {
            $this->pushBlock($id);
        }
    }

    public function handle(Handler $ajax, string $view, array $parameters = []): Handler
    {
        foreach ($this->blocks as $block) {
            try {
                $html = $this->renderBlock->render($view, $block->getId(), $parameters);
                $ajax->container(sprintf('#%s', $block->getId()))->html($html);
            } catch (AjaxcomException $exception) {
                continue;
            }
        }

        return $ajax;
    }

    public function pushBlock(string $id)
    {
        $this->blocks[] = new Block($id);
    }

    public function add(string $id): self
    {
        if (false === empty($this->blocks)) {
            $this->blocks = [];
        }

        $this->pushBlock($id);

        return $this;
    }
}
