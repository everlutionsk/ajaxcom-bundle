<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Handler;

use Everlution\Ajaxcom\Handler;

/**
 * Class ModalWindow.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class ModalWindow
{
    /** @var \Twig_Environment */
    private $twig;
    /** @var bool */
    private $isModal = false;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function handle(Handler $ajax, string $view, array $parameters = []): Handler
    {
        if ($this->isModal()) {
            $ajax->modal($this->twig->render($view, $parameters));
        }

        return $ajax;
    }

    public function closeAllModalWindows(Handler $ajax): Handler
    {
        $ajax->modal('.modal')->close();

        return $ajax;
    }

    public function renderAsModal(): self
    {
        $this->isModal = true;

        return $this;
    }

    public function isModal(): bool
    {
        return $this->isModal;
    }
}
