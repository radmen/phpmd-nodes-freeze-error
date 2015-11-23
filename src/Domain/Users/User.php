<?php

namespace Foo\Domain\Users;

use DeSmart\Support\Uuid;

class User
{

    /**
     * @var string
     */
    private $id;

    public function __construct()
    {
        $this->id = Uuid::generateUuid();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
