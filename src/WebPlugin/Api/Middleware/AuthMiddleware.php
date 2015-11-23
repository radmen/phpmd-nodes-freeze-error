<?php

namespace Foo\WebPlugin\Api\Middleware;

use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use Foo\WebPlugin\Api\Auth\Guard;
use Foo\WebPlugin\Api\Exception\ExpiredTokenException;
use Foo\WebPlugin\Api\Exception\InvalidTokenException;
use Foo\WebPlugin\Api\Jwt\TokenFactory;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class AuthMiddleware
 *
 * Tries to log in user based on JWT.
 * If this fails proper exception will be throwed.
 */
class AuthMiddleware
{

    protected $guard;

    protected $tokenFactory;

    /**
     * Time after which expired token can be refreshed
     *
     * This is stored as a string format valid with PHP date() function.
     *
     * @example '2 weeks'
     * @var string
     */
    protected $refreshTtl;

    public function __construct(Guard $guard, TokenFactory $tokenFactory, string $refreshTtl)
    {
        $this->guard = $guard;
        $this->tokenFactory = $tokenFactory;
        $this->refreshTtl = $refreshTtl;
    }

    public function handle(Request $request, callable $next)
    {
        $token = $this->tokenFactory->createFromRequest($request);
        $this->validateToken($token);

        $this->guard->loginByToken($token);

        if (false === $this->guard->isUserLogged()) {
            throw new UnauthorizedHttpException('Bearer');
        }

        return $next($request);
    }

    /**
     * Validates JWT token
     *
     * @param Token $token
     * @throws ExpiredTokenException when token has expired and can be refreshed
     * @throws InvalidTokenException when token has expired or is invalid
     */
    protected function validateToken(Token $token = null)
    {

        if (null === $token) {
            throw new InvalidTokenException;
        }

        $exp = (new \DateTime)->setTimestamp($token->getClaim('exp'));
        $now = date_create();
        $refreshTtl = \DateInterval::createFromDateString($this->refreshTtl);

        if ($now < $exp) {
            return;
        }

        if ($exp->add($refreshTtl) > $now) {
            throw new ExpiredTokenException;
        }

        throw new InvalidTokenException;
    }
}
