<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceJavaScripts.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceJavaScripts implements MutatorInterface, RenderableInterface
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
        $ajax->container(sprintf('script:not(.%s):not([nonce])', $this->persistentClass))->remove();

        try {
            $javaScripts = $this->renderBlock->render(new Block('javascripts'), $this->view, $this->parameters);
            $ajax->container('script:last-of-type')->insertAfter($javaScripts);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
