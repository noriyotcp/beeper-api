<?php

namespace BeeperApi\Validators\Users;

use BeeperApi\Validators\Validator;

class UpdateSettingsValidator extends Validator
{
    protected function rules()
    {
        $this->validator->rule('lengthBetween', 'username', 3, 30);
        $this->validator->rule('notIn', 'username', ['me']);
        $this->validator->rule('slug', 'username');

        $this->validator->rule('lengthMax', 'about', 500);

        $this->validator->rule('lengthBetween', 'password', 5, 100);
        $this->validator->rule('lengthBetween', 'new_password', 5, 100);
    }
}