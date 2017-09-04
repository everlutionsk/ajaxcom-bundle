<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

/**
 * Class Controller.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Controller extends SymfonyController
{
    use AjaxcomTrait;
}
