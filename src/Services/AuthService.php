<?php

namespace BeeperApi\Services;

use BeeperApi\Exceptions\ApiException;
use BeeperApi\Repositories\Users\UserRepository;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class AuthService
{
    private $key = "qd2UtNBu0fVyW4Z2tARCEiLV4je4lclu";
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function createTokenForUser($user)
    {
        $token = [
            "iss" => "beeper",
            "iat" => time(), //time issued
            "exp" => time() + 3600 * 4, //keep token valid for 4 hours,
            "user_id" => $user['id']
        ];

        return JWT::encode($token, $this->key);
    }

    public function getCurrentUser()
    {
        $expMessage = "Looks like your session expired or you weren't logged in, please log in again.";
        $errMessage = "There was an issue while attempting to authorize your request, please login again.";

        $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        if (!$token)
            throw new ApiException(401, [$expMessage]);

        $token = explode(" ", $token);
            if (!isset($token[1]))
                throw new ApiException(401, [$errMessage]);

        $token = $token[1];

        try {
            $decoded = JWT::decode($token, $this->key, ['HS256']);
        }
        catch (ExpiredException $e)
        {
            throw new ApiException(401, [$expMessage]);
        }
        catch (\Exception $e)
        {
            throw new ApiException(401, [$errMessage]);
        }

        $user = $this->users->first([
            'id' => $decoded->user_id
        ]);

        if (!$user)
            throw new ApiException(401, [$errMessage]);

        return $user;
    }
}