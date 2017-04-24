<?php

namespace BeeperApi\Repositories\Beeps;

use BeeperApi\Exceptions\ApiException;
use BeeperApi\Repositories\Users\UserRepository;
use MicroDB\Database;

class MicroBeep implements BeepRepository
{
    private $beepsTable;

    private $users;

    public function __construct(UserRepository $users)
    {
        $this->beepsTable = new Database($_SERVER['DOCUMENT_ROOT'] . '/data/beeps');
        $this->users = $users;
    }

    public function create($data, $createdBy)
    {
        $beep = [
            'id' => uniqid(),
            'user_id' => $createdBy['id'],
            'text' => (string) $data['text'],
            'likes' => [],
            'created_at' => time(),
        ];

        $this->beepsTable->create($beep);

        return $beep;
    }

    public function find($where = null)
    {
        return $this->beepsTable->find($where);
    }

    public function first($where)
    {
        return $this->beepsTable->first($where);
    }

    public function attachAuthors($beeps)
    {
        foreach ($beeps as &$beep) {
            $user = $this->users->first(function($u) use ($beep) {
                return $beep['user_id'] == $u['id'];
            });
            $beep['author'] = [
                'username' => $user['username'],
                'avatar' => 'http://' . $_SERVER['HTTP_HOST'] . '/public/images/' . $user['avatar']
            ];
        }

        return $beeps;
    }

    public function changeLikeState($beepID, $user)
    {
        $foundBeepID = null;
        $foundBeep = null;
        $this->beepsTable->eachId(function($id) use (&$foundBeepID, &$foundBeep, $beepID) {
            $t = $this->beepsTable->load($id);
            if ($t['id'] == $beepID) {
                $foundBeepID = $id;
                $foundBeep = $t;
            }
        });

        if (!$foundBeep)
            throw new ApiException(422, ['Beep doesn\'t exist!']);

        if (in_array($user['id'], $foundBeep['likes'])) {
            //unlike
            unset($foundBeep['likes'][array_search($user['id'], $foundBeep['likes'])]);
        } else {
            //like
            $foundBeep['likes'][] = $user['id'];
        }

        $this->beepsTable->save($foundBeepID, $foundBeep);
    }
}