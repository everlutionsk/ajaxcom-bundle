<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use DM\AjaxCom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceTitle.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceTitle
{
    /** @var RenderBlock */
    private $renderBlock;

    public function __construct(RenderBlock $renderBlock)
    {
        $this->renderBlock = $renderBlock;
    }

    public function handle(Handler $ajax, string $view, array $parameters = []): Handler
    {
        try {
            $title = $this->renderBlock->render($view, 'title', $parameters);
            $ajax->container('title')->html($title);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
