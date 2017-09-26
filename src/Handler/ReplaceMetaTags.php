<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceMetaTags.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceMetaTags
{
    /** @var RenderBlock */
    private $renderBlock;
    /** @var string */
    private $persistentClass;

    public function __construct(RenderBlock $renderBlock, string $persistentClass)
    {
        $this->renderBlock = $renderBlock;
        $this->persistentClass = $persistentClass;
    }

    public function handle(Handler $ajax, string $view, array $parameters = []): Handler
    {
        $ajax->container(sprintf('meta:not(.%s)', $this->persistentClass))->remove();

        try {
            $metaTags = $this->renderBlock->render($view, 'metatags', $parameters);
            $ajax->container('meta:last-of-type')->append($metaTags);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
