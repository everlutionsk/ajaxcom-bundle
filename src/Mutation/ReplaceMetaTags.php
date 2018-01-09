<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceMetaTags.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceMetaTags implements MutatorInterface, RenderableInterface
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
        $ajax->container(sprintf('meta:not(.%s)', $this->persistentClass))->remove();

        try {
            $metaTags = $this->renderBlock->render(new Block('metatags'), $this->view, $this->parameters);
            $ajax->container('meta:last-of-type')->insertAfter($metaTags);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
