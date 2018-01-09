<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\DataObject\Block;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceTitle.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceTitle implements MutatorInterface, RenderableInterface
{
    use RenderableTrait;

    /** @var RenderBlock */
    private $renderBlock;

    public function __construct(RenderBlock $renderBlock)
    {
        $this->renderBlock = $renderBlock;
    }

    public function mutate(Handler $ajax): Handler
    {
        try {
            $title = $this->renderBlock->render(new Block('title'), $this->view, $this->parameters);
            $ajax->container('title')->html($title);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
