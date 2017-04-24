<?php

namespace BeeperApi\Validators\Users;

use BeeperApi\Exceptions\ApiException;
use BeeperApi\Validators\Validator;

class AvatarValidator extends Validator
{
    protected function rules()
    {
        //valitron\validator doesn't have file validation, let's add those rules

        \Valitron\Validator::addRule('image', function($field, $value, array $params, array $fields) {
            if (!isset($_FILES[$field]))
                return false;

            //unsafe mime type check, don't do this in production
            //you should find the mime type of the file yourself, don't trust user's input
            if (strpos($_FILES[$field]['type'], "image") !== false)
                return true;
            else
                return false;
        }, 'must be an image');

        \Valitron\Validator::addRule('sizemax', function($field, $value, array $params, array $fields) {
            if (!isset($_FILES[$field]))
                return false;

            if ($_FILES[$field]['size'] > 2 * 1024 * 1024)
                return false;

            return true;

        }, 'must be under 2 MB');

        $this->validator->rule('required', 'avatar');
        $this->validator->rule('image', 'avatar');
        $this->validator->rule('sizemax', 'avatar');
    }
}