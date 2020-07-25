<?php

namespace App\Domains\Auth\Businesses;

use App\Businesses\BaseBusiness;
use App\Exceptions\Custom\InvalidCredentialsException;
use JwtManager\JwtManager;

class AuthGenerateBusiness extends BaseBusiness
{
    /**
     * process the request with business rules
     * @param array $data
     * @param string $type
     * @throws InvalidCredentialsException
     * @return array
     */
    public function process(
        array $data,
        string $type = 'api'
    ): array {
        $auth = $this->getFromToken(
            $data['token'],
            $data['secret']
        );

        if (empty($auth)) {
            throw new InvalidCredentialsException('Invalid credentials', 401);
        }

        return $this->generateToken(
            $auth['name'],
            $type
        );
    }

    /**
     * generate token
     * @param string $audience
     * @param string $subject
     * @return array
     */
    public function generateToken(
        string $audience,
        string $subject
    ): array {
        $jwt = $this->newJwtToken(
            $audience
        );

        $jwtToken = $jwt->generate($audience, $subject);
        $validate = date('Y-m-d H:i:s', time() + $jwt->getExpire());

        return [
            'token' => $jwtToken,
            'valid_until' => $validate,
        ];
    }

    /**
     * search on config for token and secret to authenticate
     * @param string $token
     * @param string $secret
     * @return array|null
     */
    public function getFromToken(
        string $token,
        string $secret
    ): ?array {
        $tokens = $this->getConfig('token.data');
        $hasSecret = $tokens[$token]['secret'] ?? null;

        if ($secret == $hasSecret) {
            return $tokens[$token];
        }

        return null;
    }

    /**
     * @codeCoverageIgnore
     * create and return jwt helper
     * @param string $context
     * @return JwtManager
     */
    public function newJwtToken(
        string $context
    ): JwtManager {
        return new JwtManager(
            config('app.jwt_app_secret'),
            $context
        );
    }
}
