<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Mutation;

use Everlution\AjaxcomBundle\AjaxcomException;

/**
 * Class BlockDoesNotExist.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class BlockDoesNotExist extends AjaxcomException
{
    public function __construct(string $id)
    {
        parent::__construct("Block '$id' does not exist");
    }
}
