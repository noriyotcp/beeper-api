<?php

namespace BeeperApi\Handlers;

use BeeperApi\Exceptions\ApiException;
use BeeperApi\Repositories\Beeps\BeepRepository;
use BeeperApi\Repositories\Users\UserRepository;
use BeeperApi\Services\AuthService;
use BeeperApi\Services\BeepService;
use BeeperApi\Services\MicroPaginator;
use Http\Request;
use Http\Response;

class UserBeepHandler
{
    private $request;
    private $response;
    private $beeps;
    private $users;
    private $authService;

    public function __construct(Request $request,
                                Response $response,
                                BeepRepository $beeps,
                                UserRepository $users,
                                AuthService $authService)
    {
        $this->request = $request;
        $this->response = $response;
        $this->beeps = $beeps;
        $this->users = $users;
        $this->authService = $authService;
    }


    public function getUserBeeps($username, MicroPaginator $paginator, BeepService $beepService)
    {
        $user = $this->users->first(['username' => $username]);
        if (!$user)
            throw new ApiException(404);
        $beeps = $this->beeps->find(['user_id' => $user['id']]);

        usort($beeps, function($a, $b) {
            return $a['created_at'] > $b['created_at'] ? -1 : 1;
        });

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $results = $paginator->paginate($beeps, $page);

        $results['data'] = $this->beeps->attachAuthors($results['data']);

        //check if beep liked by currently logged in user
        try {
            $user = $this->authService->getCurrentUser();
        }
        catch (\Exception $e) {
            $user = null;
        }
        foreach ($results['data'] as &$beep) {
            $beep['liked'] = $beepService->isBeepLikedByUser($beep, $user);
            $beep['likes'] = count($beep['likes']);
        }

        $this->response->setContent($results);
    }
}