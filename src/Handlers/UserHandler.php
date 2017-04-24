<?php

namespace BeeperApi\Handlers;

use BeeperApi\Repositories\Users\UserRepository;
use BeeperApi\Services\AuthService;
use BeeperApi\Validators\Users\AvatarValidator;
use BeeperApi\Validators\Users\RegisterValidator;
use BeeperApi\Validators\Users\UpdateSettingsValidator;
use Http\Request;
use Http\Response;

class UserHandler
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

    public function postRegister(RegisterValidator $validator)
    {
        $validator->validate();

        $this->users->create($this->request->getParameters());

        $this->response->setStatusCode(201);
    }

    public function getUser($username)
    {
        if ($username == 'me') {
            //return data about currently logged in user
            $user = $this->authService->getCurrentUser();
        } else {
            //find requested user
            $user = $this->users->first([
                'username' => $username
            ]);
        }

        if (!$user) {
            $this->response->setStatusCode(404);
        } else {
            $this->response->setContent([
                'username' => $user['username'],
                'about'    => $user['about'],
                'avatar'   => 'http://' . $_SERVER['HTTP_HOST'] . '/public/images/' . $user['avatar']
            ]);
        }

    }

    public function putSettings(UpdateSettingsValidator $validator)
    {
        $validator->validate();

        $user = $this->authService->getCurrentUser();

        $params = $this->request->getParameters();

        $this->users->update($user['id'], ['username' => $params['username'], 'about' => $params['about']]);
    }

    public function putAvatar(AvatarValidator $validator)
    {
        $validator->validate();

        $user = $this->authService->getCurrentUser();

        if ($user['avatar'] != "noavatar.jpg") {
            unlink(getcwd() . '/public/images/' . $user['avatar']);
        }

        $ext = explode('.', $_FILES['avatar']['name']);
        $avatarName = $user['id'] . '_' . uniqid() . '.' . strtolower($ext[1]);
        copy($_FILES['avatar']['tmp_name'], 'public/images/' . $avatarName);

        $this->users->update($user['id'], ['avatar' => $avatarName]);

        $this->response->setContent([
            'avatar'   => 'http://' . $_SERVER['HTTP_HOST'] . '/public/images/' . $avatarName,
        ]);
    }
}
