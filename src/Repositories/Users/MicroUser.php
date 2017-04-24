<?php

namespace BeeperApi\Repositories\Users;

use BeeperApi\Exceptions\ApiException;
use MicroDB\Database;

class MicroUser implements UserRepository
{
    private $usersTable;

    public function __construct()
    {
        $this->usersTable = new Database($_SERVER['DOCUMENT_ROOT'] . '/data/users');
    }

    public function create($data)
    {
        if (
        $this->usersTable->find(function ($user) use ($data) {
            return $user['username'] == $data['username'] || $user['email'] == $data['email'];
        })
        ) {
            throw new ApiException(422, ['Username and/or email already taken']);
        }

        $newUser = [
            'id'       => uniqid(),
            'username' => $data['username'],
            'email'    => $data['email'],
            //yep, i'm just storing plain text passwords because this is a demo api using microDB
            //of course, don't do this for production systems, always hash passwords!
            'password' => $data['password'],
            'about'    => "I like beeping",
            'avatar'   => "noavatar.jpg"
        ];

        $this->usersTable->create($newUser);
    }

    public function first($where)
    {
        return $this->usersTable->first($where);
    }

    public function find($where)
    {
        return $this->usersTable->find($where);
    }

    public function update($userID, $data)
    {
        $foundUserID = null;
        $foundUser = null;
        $this->usersTable->eachId(function($id) use (&$foundUserID, &$foundUser, $userID) {
            $t = $this->usersTable->load($id);
            if ($t['id'] == $userID) {
                $foundUserID = $id;
                $foundUser = $t;
            }
        });

        if (!$foundUserID)
            throw new \RuntimeException("User couldn't be found");

        //update username
        if (isset($data['username'])) {
            $t = $this->usersTable->first(function ($user) use ($data) {
                return $user['username'] == $data['username'];
            });

            if ($t && $t['id'] != $userID)
                throw new ApiException(422, ['Username already taken']);

            $foundUser['username'] = $data['username'];
        }

        //update about
        if (isset($data['about'])) {
            $foundUser['about'] = $data['about'];
        }

        //update password
        if (isset($data['password'])) {
            if (!isset($data['new_password']) || !$data['new_password'])
                throw new ApiException(422, ['You must enter new password too']);

            $foundUser['password'] = $data['new_password'];
        }

        //update avatar filename
        if (isset($data['avatar'])) {
            $foundUser['avatar'] = $data['avatar'];
        }

        $this->usersTable->save($foundUserID, $foundUser);
    }
}