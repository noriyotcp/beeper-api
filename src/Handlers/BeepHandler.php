<?php

namespace BeeperApi\Handlers;

use BeeperApi\Repositories\Beeps\BeepRepository;
use BeeperApi\Services\AuthService;
use BeeperApi\Services\BeepService;
use BeeperApi\Services\MicroPaginator;
use BeeperApi\Validators\Beeps\CreateBeepValidator;
use Http\Request;
use Http\Response;

class BeepHandler
{
    private $request;
    private $response;
    private $beeps;
    private $authService;

    public function __construct(Request $request,
                                Response $response,
                                BeepRepository $beeps,
                                AuthService $authService)
    {
        $this->request = $request;
        $this->response = $response;
        $this->beeps = $beeps;
        $this->authService = $authService;
    }

    public function getAllBeeps(MicroPaginator $paginator, BeepService $beepService)
    {
        $user = $this->authService->getCurrentUser();

        $beeps = $this->beeps->find(function(){return true;});
        usort($beeps, function($a, $b) {
            return $a['created_at'] > $b['created_at'] ? -1 : 1;
        });

        $page = (int) isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $results = $paginator->paginate($beeps, $page);

        $results['data'] = $this->beeps->attachAuthors($results['data']);

        //check if beep is liked by currently logged in user
        foreach ($results['data'] as &$beep) {
            $beep['liked'] = $beepService->isBeepLikedByUser($beep, $user);
            $beep['likes'] = count($beep['likes']);
        }

        $this->response->setContent($results);
    }

    public function postBeep(CreateBeepValidator $validator)
    {
        $validator->validate();

        $user = $this->authService->getCurrentUser();
        $newBeep = $this->beeps->create($this->request->getParameters(), $user);

        $newBeep['liked'] = false;
        $newBeep['likes'] = 0;
        $newBeep['author'] = $user;
        $newBeep['author']['avatar'] = 'http://' . $_SERVER['HTTP_HOST'] . '/public/images/' . $user['avatar'];

        $this->response->setContent($newBeep);
        $this->response->setStatusCode(201);
    }

    public function patchLikeBeep($id)
    {
        $user = $this->authService->getCurrentUser();

        $this->beeps->changeLikeState($id, $user);

        $this->response->setStatusCode(200);
    }
}