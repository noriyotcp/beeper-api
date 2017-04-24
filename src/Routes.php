<?php

return [
    ['POST', '/auth', ['BeeperApi\Handlers\AuthHandler', 'login']],

    ['POST', '/users', ['BeeperApi\Handlers\UserHandler', 'postRegister']],
    ['GET', '/users/{username}', ['BeeperApi\Handlers\UserHandler', 'getUser']],
    ['PUT', '/users/me', ['BeeperApi\Handlers\UserHandler', 'putSettings']],
    ['PUT', '/users/me/avatar', ['BeeperApi\Handlers\UserHandler', 'putAvatar']],

    ['GET', '/users/{username}/beeps', ['BeeperApi\Handlers\UserBeepHandler', 'getUserBeeps']],

    ['GET', '/beeps', ['BeeperApi\Handlers\BeepHandler', 'getAllBeeps']],
    ['POST', '/beeps', ['BeeperApi\Handlers\BeepHandler', 'postBeep']],
    ['GET', '/beeps/{id}', ['BeeperApi\Handlers\BeepHandler', 'getBeep']],
    ['PATCH', '/beeps/{id}/like', ['BeeperApi\Handlers\BeepHandler', 'patchLikeBeep']],

    //['POST', '/beeps/{id}/comments', ['BeeperApi\Handlers\BeepCommentHandler', 'postBeepComment']],
];
