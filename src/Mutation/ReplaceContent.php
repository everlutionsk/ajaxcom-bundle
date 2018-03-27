<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceBlockContent.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceContent implements MutatorInterface, RenderableInterface
{
    use RenderableTrait;

    /** @var RenderBlock */
    private $renderBlock;
    private $data = [];

    public function __construct(RenderBlock $renderBlock)
    {
        $this->renderBlock = $renderBlock;
    }

    public function mutate(Handler $ajax): Handler
    {
        foreach ($this->data as $item) {
            $block = $item['block'];
            try {
                $html = $this->renderBlock->render($block, $this->view, $this->parameters);
            } catch (AjaxcomException $exception) {
                continue;
            }

            $ajax->container($item['selector'])->html($html);
        }

        return $ajax;
    }

    public function add(string $selector, string $blockId): self
    {
        $this->data[] = ['selector' => $selector, 'block' => new Block($blockId)];

        return $this;
    }
}
