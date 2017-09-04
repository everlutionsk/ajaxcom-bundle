<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

/**
 * Class BaseController.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Controller extends SymfonyController
{
    use AjaxcomTrait;
}
