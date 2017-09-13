<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\DataObject;

/**
 * Class Block.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Block
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
