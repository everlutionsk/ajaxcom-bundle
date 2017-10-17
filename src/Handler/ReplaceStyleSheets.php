<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceStyleSheets.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceStyleSheets
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
        $ajax->container(sprintf('style:not(.%s):not([nonce])', $this->persistentClass))->remove();

        try {
            $styleSheets = $this->renderBlock->render($view, 'stylesheets', $parameters);
            $ajax->container('style:last-of-type')->append($styleSheets);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
