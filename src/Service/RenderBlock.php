<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Service;

use Everlution\AjaxcomBundle\DataObject\Block;

/**
 * Class RenderBlock.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class RenderBlock
{
    /** @var \Twig\Environment */
    private $twig;

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(Block $block, string $template, array $parameters = []): string
    {
        $blockId = $this->getNormalizedId($block);
        $view = $this->twig->load($template);
        if (false === $view->hasBlock($blockId)) {
            throw new TemplateDoesNotContainBlock();
        }

        $html = $view->renderBlock($blockId, $parameters);
        if (empty($html) && false === $block->shouldRefresh()) {
            throw new BlockIsEmpty();
        }

        return $html;
    }

    private function getNormalizedId(Block $block): string
    {
        return str_replace('-', '_', $block->getId());
    }
}
