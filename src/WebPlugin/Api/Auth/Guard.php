<?php

namespace Foo\WebPlugin\Api\Auth;

use Lcobucci\JWT\Token;
use Foo\Domain\Users\Exception\UserNotFoundException;
use Foo\Domain\Users\User;
use Foo\Domain\Users\Repository\UsersRepositoryInterface;

class Guard
{

    protected $usersRepository;

    protected $user;

    public function __construct(UsersRepositoryInterface $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function loginByToken(Token $token)
    {
        $uid = $token->getClaim('uid');

        try {
            $this->user = $this->usersRepository->getById($uid);
        } catch (UserNotFoundException $e) {
            // do nothing here
        }
    }

    public function loginUser(User $user)
    {
        $this->user = $user;
    }

    public function isUserLogged(): bool
    {
        return null !== $this->user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
