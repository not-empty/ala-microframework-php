<?php

namespace App\Http\Middlewares;

use App\Exceptions\Custom\InvalidCredentialsException;
use Closure;
use JwtManager\JwtManager;

class AuthenticateJwt
{
    /**
     * handle an incoming request,
     *   authenticating with jwt token
     * @param Request $request
     * @param Closure $next
     * @throws InvalidCredentialsException
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $token = $request->headers->get('Authorization') ?? null;
        $context = $request->headers->get('Context') ?? null;

        if (empty($token) || empty($context)) {
            throw new InvalidCredentialsException('Missing authorization', 401);
        }

        $token = str_replace('Bearer ', '', $token);

        $jwt = $this->newJwtToken(
            $context
        );
        $jwt->isValid($token);
        $jwt->isOnTime($token);

        $data = $jwt->decodePayload($token);
        $audience = $data['aud'];
        $subject = $data['sub'];

        if ($subject !== 'api') {
            throw new InvalidCredentialsException('Invalid subject', 401);
        }

        if ($context !== $audience) {
            throw new InvalidCredentialsException('Invalid context', 401);
        }

        $exp = $data['exp'];
        $validUntil = date('Y-m-d H:i:s', $exp);

        $needRefresh = $jwt->tokenNeedToRefresh($token);
        if ($needRefresh) {
            $token = $jwt->generate($audience, $subject);
            $validUntil = date('Y-m-d H:i:s', time() + 900);
        }

        $request->jwtToken = [
            'token' => $token,
            'valid_until' => $validUntil,
        ];

        $request->info = config('version.info');

        return $next($request);
    }

    /**
     * @codeCoverageIgnore
     * create and return a new jwt helper
     * @param string $context
     * @return object
     */
    public function newJwtToken(
        string $context
    ) {
        return new JwtManager(
            config('app.jwt_app_secret'),
            $context
        );
    }
}
