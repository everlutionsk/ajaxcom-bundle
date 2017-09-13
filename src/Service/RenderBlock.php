<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Service;

/**
 * Class RenderBlock.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class RenderBlock
{
    /** @var \Twig_Environment */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(string $template, string $blockId, array $parameters = []): string
    {
        $template = $this->twig->load($template);
        if (false === $template->hasBlock($blockId)) {
            throw new TemplateDoesNotContainBlock();
        }

        $html = $template->renderBlock($blockId, $parameters);
        if (empty($html)) {
            throw new BlockIsEmpty();
        }

        return $html;
    }
}
