<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceStyleSheets.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceStyleSheets implements MutatorInterface, RenderableInterface
{
    use RenderableTrait;

    /** @var RenderBlock */
    private $renderBlock;
    /** @var string */
    private $persistentClass;

    public function __construct(RenderBlock $renderBlock, string $persistentClass)
    {
        $this->renderBlock = $renderBlock;
        $this->persistentClass = $persistentClass;
    }

    public function mutate(Handler $ajax): Handler
    {
        $ajax->container(sprintf('style:not(.%s):not([nonce])', $this->persistentClass))->remove();

        try {
            $styleSheets = $this->renderBlock->render($this->view, 'stylesheets', $this->parameters);
            $ajax->container('style:last-of-type')->insertAfter($styleSheets);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
