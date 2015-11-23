<?php

namespace Foo\Domain\Users\Repository;

use Foo\Domain\Users\User;

interface UsersRepositoryInterface
{
    /**
     * Get user by id
     *
     * @param string $id
     * @return User
     * @throws \Foo\Domain\Users\Exception\UserNotFoundException
     */
    public function getById(string $id): User;
}
