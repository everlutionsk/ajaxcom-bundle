<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use Everlution\Ajaxcom\Handler;
use Everlution\AjaxcomBundle\AjaxcomException;
use Everlution\AjaxcomBundle\Service\RenderBlock;

/**
 * Class ReplaceJavaScripts.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ReplaceJavaScripts
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
        $ajax->container(sprintf('script:not(.%s, [nonce])', $this->persistentClass))->remove();

        try {
            $javaScripts = $this->renderBlock->render($view, 'javascripts', $parameters);
            $ajax->container('script:last-of-type')->append($javaScripts);
        } catch (AjaxcomException $exception) {
            // do nothing
        } finally {
            return $ajax;
        }
    }
}
