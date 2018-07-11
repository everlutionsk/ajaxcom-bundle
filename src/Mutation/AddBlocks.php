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

        $this->defaultBlocks = array_map(
            function (array $data) {
                $block = new Block($data['id']);
                if ($data['refresh']) {
                    $block->refresh();
                }

                return $block;
            },
            $blocksToRender
        );
    }

    public function mutate(Handler $ajax): Handler
    {
        foreach ($this->getBlocks() as $block) {
            try {
                $html = $this->renderBlock->render($block, $this->view, $this->parameters);
            } catch (AjaxcomException $exception) {
                continue;
            }

            $ajax->container(sprintf('#%s', $block->getId()))->html($html);
        }

        return $ajax;
    }

    public function add(string $id): self
    {
        $this->blocks[$id] = new Block($id);

        return $this;
    }

    public function remove(string $id): self
    {
        unset($this->blocks[$id]);

        return $this;
    }

    public function refresh(string $id): self
    {
        if (false === array_key_exists($id, $this->blocks)) {
            throw new BlockDoesNotExist($id);
        }

        $this->defaultBlocks[$id]->refresh();

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
