<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\Ajaxcom\Handler;

/**
 * Interface MutatorInterface.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
interface MutatorInterface
{
    public function mutate(Handler $ajaxcom): Handler;
}
