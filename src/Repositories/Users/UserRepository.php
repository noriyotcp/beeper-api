<?php

namespace BeeperApi\Repositories\Users;

interface UserRepository
{
    public function create($data);

    public function first($where);

    public function find($where);

    public function update($userID, $data);
}