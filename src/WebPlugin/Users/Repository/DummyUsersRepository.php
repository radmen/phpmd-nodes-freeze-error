<?php

namespace Foo\WebPlugin\Users\Repository;

use Foo\Domain\Users\Exception\UserNotFoundException;
use Foo\Domain\Users\Repository\UsersRepositoryInterface;
use Foo\Domain\Users\User;

/**
 * Dummy representation of users repository
 *
 * @TODO remove it later!
 */
class DummyUsersRepository implements UsersRepositoryInterface
{

    public function getById(string $id): User
    {
        throw new UserNotFoundException;
    }
}
