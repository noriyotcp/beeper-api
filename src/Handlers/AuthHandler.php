<?php

namespace BeeperApi\Handlers;

use BeeperApi\Exceptions\ApiException;
use BeeperApi\Repositories\Users\UserRepository;
use BeeperApi\Services\AuthService;
use Http\Request;
use Http\Response;

class AuthHandler
{
    private $request;
    private $response;
    private $users;
    private $authService;

    public function __construct(Request $request,
                                Response $response,
                                UserRepository $users,
                                AuthService $authService)
    {
        $this->request = $request;
        $this->response = $response;
        $this->users = $users;
        $this->authService = $authService;
    }

    public function login()
    {
        $user = $this->users->first([
            'username' => $this->request->getParameter('username'),
            'password' => $this->request->getParameter('password')
        ]);

        if (!$user)
            throw new ApiException(422, ['Wrong username/password combination']);

        $token = $this->authService->createTokenForUser($user);

        $this->response->setContent(['token' => $token]);
    }
}