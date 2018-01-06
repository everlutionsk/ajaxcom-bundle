<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class AddBlocks.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class AddBlocks implements MutatorInterface, RenderableInterface
{
    use RenderableTrait;

    /** @var RenderBlock */
    private $renderBlock;
    /** @var Block[] */
    private $blocks = [];
    /** @var Block[] */
    private $defaultBlocks = [];

    public function __construct(RenderBlock $renderBlock, array $blocksToRender = [])
    {
        $this->renderBlock = $renderBlock;

        foreach ($blocksToRender as $id) {
            $this->defaultBlocks[] = new Block($id);
        }
    }

    public function mutate(Handler $ajax): Handler
    {
        foreach ($this->getBlocks() as $block) {
            try {
                $blockId = str_replace('-', '_', $block->getId());
                $html = $this->renderBlock->render($this->view, $blockId, $this->parameters);
                $ajax->container(sprintf('#%s', $block->getId()))->html($html);
            } catch (AjaxcomException $exception) {
                continue;
            }
        }

        return $ajax;
    }

    public function add(string $id): self
    {
        $this->blocks[] = new Block($id);

        return $this;
    }

    /**
     * @return Block[]
     */
    private function getBlocks(): array
    {
        return empty($this->blocks) ? $this->defaultBlocks : $this->blocks;
    }
}
