<?php

namespace BeeperApi\Services;


class BeepService
{
    public function isBeepLikedByUser($beep, $user = null)
    {
        if (!$user)
            return false;

        if (in_array($user['id'], $beep['likes']))
            return true;
        else
            return false;
    }
}