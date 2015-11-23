<?php

namespace Foo\WebPlugin\Api\Jwt;

use Illuminate\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac;
use Lcobucci\JWT\Token;
use Foo\Domain\Users\User;
use Foo\WebPlugin\Api\Exception\InvalidTokenException;

class TokenFactory
{

    /**
     * Token expiration ttl
     *
     * @example '1 hour'
     * @var string
     */
    protected $tokenExpTtl;

    /**
     * @var string
     */
    protected $tokenSecret;

    public function __construct(string $tokenExpTtl, string $tokenSecret)
    {
        $this->tokenExpTtl = $tokenExpTtl;
        $this->tokenSecret = $tokenSecret;
    }

    /**
     * Generates token based on user entity and current request
     *
     * @param User $user
     * @param Request $request
     * @return Token
     */
    public function createForUser(User $user, Request $request): Token
    {
        return (new Builder)->setIssuer($request->url())
            ->setIssuedAt(time())
            ->setExpiration(strtotime($this->tokenExpTtl))
            ->set('uid', $user->getId())
            ->sign($this->getSigner(), $this->tokenSecret)
            ->getToken();
    }

    /**
     * Creates token based on 'Authorization' header
     *
     * @param Request $request
     * @return Token|null
     * @throws \UnexpectedValueException when header format is invalid
     * @throws InvalidTokenException when token didn't pass HMAC verification
     */
    public function createFromRequest(Request $request)
    {
        $authorization = $request->header('authorization');

        if (null === $authorization) {
            return null;
        }

        $token = preg_replace('/^Bearer\s+/', '', $authorization);

        try {
            $token = (new Parser)->parse($token);
        } catch (\InvalidArgumentException $exception) {
            throw new \UnexpectedValueException($exception->getMessage());
        }

        $signer = $this->getSigner();

        if (false === $token->verify($signer, $this->tokenSecret)) {
            throw new InvalidTokenException;
        }

        return $token;
    }

    /**
     * Return token signer
     *
     * @return Hmac
     */
    protected function getSigner(): Hmac
    {
        return new Hmac\Sha256;
    }
}
